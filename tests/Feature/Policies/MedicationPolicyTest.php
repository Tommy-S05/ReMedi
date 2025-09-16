<?php

declare(strict_types=1);

namespace Tests\Feature\Policies;

use App\Enums\ShareStatusEnum;
use App\Models\Medication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

describe('MedicationPolicy', function () {
    describe('view ability', function () {
        it('allows the owner to view their own medication', function () {
            $owner = User::factory()->create();
            $medication = Medication::factory()->create(['user_id' => $owner->id]);

            actingAs($owner);

            $this->assertTrue($owner->can('view', $medication));
        });

        it('allows a user with whom the medication is shared to view it', function () {
            $owner = User::factory()->create();
            $recipient = User::factory()->create();
            $medication = Medication::factory()->create(['user_id' => $owner->id]);

            // Crear un registro de compartición aceptado
            $medication->shares()->create([
                'owner_user_id' => $owner->id,
                'shared_with_user_id' => $recipient->id,
                'shared_with_email' => $recipient->email,
                'status' => ShareStatusEnum::ACCEPTED,
                'token' => 'test-token',
            ]);

            actingAs($recipient);

            $this->assertTrue($recipient->can('view', $medication));
        });

        it('denies a user if the share is pending', function () {
            $owner = User::factory()->create();
            $recipient = User::factory()->create();
            $medication = Medication::factory()->create(['user_id' => $owner->id]);

            // Crear un registro de compartición pendiente
            $medication->shares()->create([
                'owner_user_id' => $owner->id,
                'shared_with_user_id' => $recipient->id,
                'shared_with_email' => $recipient->email,
                'status' => ShareStatusEnum::PENDING,
                'token' => 'test-token',
            ]);

            actingAs($recipient);

            $this->assertFalse($recipient->can('view', $medication));
        });

        it('denies a random user from viewing the medication', function () {
            $owner = User::factory()->create();
            $randomUser = User::factory()->create();
            $medication = Medication::factory()->create(['user_id' => $owner->id]);

            actingAs($randomUser);

            $this->assertFalse($randomUser->can('view', $medication));
        });
    });
});
