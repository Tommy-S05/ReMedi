<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\ShareStatusEnum;
use App\Models\Medication;
use App\Models\ResourceShare;
use App\Models\User;
use App\Notifications\ResourceSharedNotification;
use App\Services\SharingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use InvalidArgumentException;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->owner = User::factory()->create();
    $this->recipient = User::factory()->create();
    $this->medication = Medication::factory()->create(['user_id' => $this->owner->id]);
    $this->sharingService = app(SharingService::class);
});

describe('SharingService::createInvitation', function () {
    it('successfully creates an invitation for a new user and sends a notification', function () {
        // Arrange
        Notification::fake();
        $recipientEmail = 'new@example.com';

        // Act
        $share = $this->sharingService->createInvitation($this->owner, $this->medication, $recipientEmail);

        // Assert
        $this->assertDatabaseHas('resource_shares', [
            'owner_user_id' => $this->owner->id,
            'shared_with_email' => $recipientEmail,
            'shared_with_user_id' => null,
            'shareable_id' => $this->medication->id,
            'shareable_type' => Medication::class,
            'status' => ShareStatusEnum::PENDING->value,
        ]);

        // Assert que la fecha de expiración se ha establecido
        expect($share->expires_at)->toBeInstanceOf(Carbon::class)
            ->and($share->expires_at->isFuture())->toBeTrue();

        Notification::assertSentTo(
            new AnonymousNotifiable(),
            ResourceSharedNotification::class,
            fn ($notification, $channels, $notifiable) => $notifiable->routes['mail'] === $recipientEmail
        );
    });

    it('successfully creates a share invitation and sends a notification', function () {
        // Arrange
        Notification::fake();
        $recipientEmail = $this->recipient->email;

        // Act
        $share = $this->sharingService->createInvitation($this->owner, $this->medication, $recipientEmail);

        // Assert
        $this->assertDatabaseCount('resource_shares', 1);
        $this->assertDatabaseHas('resource_shares', [
            'owner_user_id' => $this->owner->id,
            'shared_with_email' => $recipientEmail,
            'shared_with_user_id' => $this->recipient->id,
            'shareable_id' => $this->medication->id,
            'shareable_type' => get_class($this->medication),
            'status' => ShareStatusEnum::PENDING->value,
        ]);

        Notification::assertSentTo(
            new AnonymousNotifiable(),
            ResourceSharedNotification::class,
            fn ($notification, $channels, $notifiable) => $notifiable->routes['mail'] === $recipientEmail
        );

        expect($share)->toBeInstanceOf(ResourceShare::class)
            ->and($share->owner_user_id)->toBe($this->owner->id)
            ->and($share->shared_with_email)->toBe($recipientEmail)
            ->and($share->status)->toBe(ShareStatusEnum::PENDING);

        expect($share->expires_at)->toBeInstanceOf(Carbon::class)
            ->and($share->expires_at->isFuture())->toBeTrue();
    });

    it('throws an exception when a user tries to share a resource with themselves', function () {
        expect(
            fn () => $this->sharingService->createInvitation($this->owner, $this->medication, $this->owner->email)
        )->toThrow(
            InvalidArgumentException::class,
            'You cannot share a resource with yourself.'
        );
    });

    it('throws an exception if the resource is already shared with the same email', function () {
        $recipientEmail = $this->recipient->email;
        // Arrange
        $this->sharingService->createInvitation($this->owner, $this->medication, $recipientEmail);

        // Act $ Assert
        expect(
            fn () => $this->sharingService->createInvitation($this->owner, $this->medication, $recipientEmail)
        )->toThrow(
            InvalidArgumentException::class,
            'This resource has already been shared with this user.'
        );
    });
});

describe('SharingService::acceptInvitation', function () {
    it('successfully accepts a valid invitation', function () {
        // Arrange
        $invitation = $this->sharingService->createInvitation($this->owner, $this->medication, $this->recipient->email);

        // Act
        $acceptedShare = $this->sharingService->acceptInvitation($invitation->token, $this->recipient);

        // Assert
        expect($acceptedShare->status)->toBe(ShareStatusEnum::ACCEPTED);
        expect($acceptedShare->shared_with_user_id)->toBe($this->recipient->id);
    });

    it('throws an exception for an invalid token', function () {
        $this->sharingService->acceptInvitation('invalid-token', $this->recipient);
    })->throws(InvalidArgumentException::class, 'This invitation is invalid.');

    it('throws an exception for an expired invitation', function () {
        $invitation = ResourceShare::factory()->create([
            'owner_user_id' => $this->owner->id,
            'shareable_id' => $this->medication->id,
            'shareable_type' => Medication::class,
            'shared_with_email' => $this->recipient->email,
            'expires_at' => now()->subDay(),
        ]);

        $this->sharingService->acceptInvitation($invitation->token, $this->recipient);
    })->throws(InvalidArgumentException::class, 'This invitation has expired.');

    it('throws an exception for an already actioned invitation', function () {
        $invitation = $this->sharingService->createInvitation($this->owner, $this->medication, $this->recipient->email);

        // Aceptar la primera vez
        $this->sharingService->acceptInvitation($invitation->token, $this->recipient);
        // Intentar aceptar de nuevo
        $this->sharingService->acceptInvitation($invitation->token, $this->recipient);
    })->throws(InvalidArgumentException::class, 'This invitation has already been actioned.');

    it('throws an exception if the accepting user email does not match', function () {
        $recipientEmail = 'intended.recipient@example.com';
        $wrongUser = User::factory()->create(['email' => 'wrong.user@example.com']);
        $invitation = $this->sharingService->createInvitation($this->owner, $this->medication, $recipientEmail);

        $this->sharingService->acceptInvitation($invitation->token, $wrongUser);
    })->throws(InvalidArgumentException::class, 'This invitation is intended for another user.');
});
