<?php

declare(strict_types=1);

namespace Tests\Feature\Policies;

use App\Enums\ShareStatusEnum;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

describe('PrescriptionPolicy', function () {
    describe('view ability', function () {
        it('allows the owner to view their own prescription', function () {
            $owner = User::factory()->create();
            $prescription = Prescription::factory()->create(['user_id' => $owner->id]);

            actingAs($owner);

            $this->assertTrue($owner->can('view', $prescription));
        });

        it('allows a user with whom the prescription is shared to view it', function () {
            $owner = User::factory()->create();
            $recipient = User::factory()->create();
            $prescription = Prescription::factory()->create(['user_id' => $owner->id]);

            // Crear un registro de compartición aceptado
            $prescription->shares()->create([
                'owner_user_id' => $owner->id,
                'shared_with_user_id' => $recipient->id,
                'shared_with_email' => $recipient->email,
                'status' => ShareStatusEnum::ACCEPTED,
                'token' => 'test-token',
            ]);

            actingAs($recipient);

            $this->assertTrue($recipient->can('view', $prescription));
        });

        it('denies a user if the share is pending', function () {
            $owner = User::factory()->create();
            $recipient = User::factory()->create();
            $prescription = Prescription::factory()->create(['user_id' => $owner->id]);

            // Crear un registro de compartición pendiente
            $prescription->shares()->create([
                'owner_user_id' => $owner->id,
                'shared_with_user_id' => $recipient->id,
                'shared_with_email' => $recipient->email,
                'status' => ShareStatusEnum::PENDING,
                'token' => 'test-token',
            ]);

            actingAs($recipient);

            $this->assertFalse($recipient->can('view', $prescription));
        });

        it('denies a random user from viewing the prescription', function () {
            $owner = User::factory()->create();
            $randomUser = User::factory()->create();
            $prescription = Prescription::factory()->create(['user_id' => $owner->id]);

            actingAs($randomUser);

            $this->assertFalse($randomUser->can('view', $prescription));
        });
    });
});
