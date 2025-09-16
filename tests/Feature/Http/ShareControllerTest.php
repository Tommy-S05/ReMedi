<?php

declare(strict_types=1);

namespace Tests\Feature\Http;

use App\Enums\ShareStatusEnum;
use App\Models\Medication;
use App\Models\Prescription;
use App\Models\ResourceShare;
use App\Models\User;
use App\Notifications\ResourceSharedNotification;
use App\Services\SharingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

// for guest redirect sanity check

uses(RefreshDatabase::class)->group('feature', 'shares');

beforeEach(function () {
    $this->owner = User::factory()->create();
    $this->recipient = User::factory()->create();
    $this->medication = Medication::factory()->create(['user_id' => $this->owner->id]);
    $this->prescription = Prescription::factory()->create(['user_id' => $this->owner->id]);
    $this->sharingService = app(SharingService::class);
});

describe('POST /shares', function () {
    it('allows an authenticated user to share their own medication', function () {
        Notification::fake();

        actingAs($this->owner)
            ->post(route('shares.store'), [
                'email' => 'friend@example.com',
                'shareable_type' => 'medication',
                'shareable_id' => $this->medication->id,
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('resource_shares', [
            'owner_user_id' => $this->owner->id,
            'shared_with_email' => 'friend@example.com',
            'shareable_id' => $this->medication->id,
            'shareable_type' => Medication::class,
        ]);

        Notification::assertSentTo(
            new AnonymousNotifiable,
            ResourceSharedNotification::class
        );
    });

    it('allows an authenticated user to share their own prescription', function () {
        Notification::fake();

        actingAs($this->owner)
            ->post(route('shares.store'), [
                'email' => 'friend@example.com',
                'shareable_type' => 'prescription',
                'shareable_id' => $this->prescription->id,
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('resource_shares', [
            'owner_user_id' => $this->owner->id,
            'shared_with_email' => 'friend@example.com',
            'shareable_id' => $this->prescription->id,
            'shareable_type' => Prescription::class,
        ]);

        Notification::assertSentTo(
            new AnonymousNotifiable,
            ResourceSharedNotification::class
        );
    });

    it('prevents an authenticated user from sharing a resource they do not own', function () {
        $anotherUser = User::factory()->create();

        actingAs($anotherUser)
            ->post(route('shares.store'), [
                'email' => 'friend@example.com',
                'shareable_type' => 'medication',
                'shareable_id' => $this->medication->id,
            ])
            ->assertForbidden();
    });

    it('prevents a user from sharing a resource with themselves', function () {
        actingAs($this->owner)
            ->post(route('shares.store'), [
                'email' => $this->owner->email,
                'shareable_type' => 'medication',
                'shareable_id' => $this->medication->id,
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('email');
    });

    it('fails validation for invalid data', function (array $invalidData, array|string $expectedErrors) {
        actingAs($this->owner)
            ->post(route('shares.store'), $invalidData)
            ->assertSessionHasErrors($expectedErrors);
    })->with([
        'missing email' => [['shareable_type' => 'medication', 'shareable_id' => 1], 'email'],
        'invalid email' => [['email' => 'not-an-email', 'shareable_type' => 'medication', 'shareable_id' => 1], 'email'],
        'missing shareable_type' => [['email' => 'test@test.com', 'shareable_id' => 1], 'shareable_type'],
        'invalid shareable_type' => [['email' => 'test@test.com', 'shareable_type' => 'invalid-type', 'shareable_id' => 1], 'shareable_type'],
        'missing shareable_id' => [['email' => 'test@test.com', 'shareable_type' => 'medication'], 'shareable_id'],
        'non-existent shareable_id' => fn () => [['email' => 'test@test.com', 'shareable_type' => 'medication', 'shareable_id' => $this->medication->id + 999], 'shareable_id'],
        'mismatched shareable_id and type' => function () {
            // Creamos una nueva prescripción para garantizar que su ID sea diferente al del medicamento.
            $mismatchedPrescription = Prescription::factory()->create(['user_id' => $this->owner->id]);

            return [['email' => 'test@test.com', 'shareable_type' => 'medication', 'shareable_id' => $mismatchedPrescription->id], 'shareable_id'];
        },
    ]);

    it('validates shareable_id exists in correct table', function () {
        actingAs($this->owner)
            ->post(route('shares.store'), [
                'email' => 'friend@example.com',
                'shareable_type' => 'medication',
                'shareable_id' => 99999,
            ])
            ->assertSessionHasErrors(['shareable_id']);
    });

    it('redirects guests to login', function () {
        post(route('shares.store'), [])
            ->assertRedirect(route('login'));
    });
});

describe('GET /shares/accept/{token}', function () {
    it('allows a logged-in user to accept a valid invitation', function () {
        $invitation = $this->sharingService->createInvitation($this->owner, $this->medication, $this->recipient->email);

        $signedUrl = URL::temporarySignedRoute(
            'shares.accept', now()->addMinutes(5), ['token' => $invitation->token]
        );

        actingAs($this->recipient)
            ->get($signedUrl)
            ->assertRedirect(route('medications.show', $this->medication->id))
            ->assertSessionHas('success', 'You now have access to the shared resource.');

        $this->assertDatabaseHas('resource_shares', [
            'id' => $invitation->id,
            'status' => ShareStatusEnum::ACCEPTED->value,
            'shared_with_user_id' => $this->recipient->id,
        ]);
    });

    it('redirects a guest to the login page with an intended URL', function () {
        $invitation = ResourceShare::factory()->create();
        $signedUrl = URL::temporarySignedRoute(
            'shares.accept', now()->addMinutes(5), ['token' => $invitation->token]
        );

        get($signedUrl)
            ->assertRedirect(route('login'))
            ->assertSessionHas('intended', $signedUrl);
    });

    it('shows an error page for an invalid token', function () {
        $user = User::factory()->create();
        $signedUrl = URL::temporarySignedRoute(
            'shares.accept', now()->addMinutes(5), ['token' => 'invalid-token']
        );

        actingAs($user)
            ->get($signedUrl)
            ->assertStatus(400);
    });
});
