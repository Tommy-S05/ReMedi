<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Enums\ShareStatusEnum;
use App\Models\Medication;
use App\Models\ResourceShare;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('ResourceShare Model', function () {
    it('belongs to an owner user', function () {
        $owner = User::factory()->create();
        $share = ResourceShare::factory()->for($owner, 'owner')->create();

        expect($share->owner)->toBeInstanceOf(User::class)
            ->and($share->owner->id)->toBe($owner->id);
    });

    it('belongs to a shared with user', function () {
        $sharedWith = User::factory()->create();
        $share = ResourceShare::factory()->for($sharedWith, 'sharedWithUser')->create();

        expect($share->sharedWithUser)->toBeInstanceOf(User::class)
            ->and($share->sharedWithUser->id)->toBe($sharedWith->id);
    });

    it('can have a shareable resource', function () {
        $medication = Medication::factory()->create();
        $share = ResourceShare::factory()->create([
            'shareable_id' => $medication->id,
            'shareable_type' => get_class($medication),
        ]);

        expect($share->shareable)->toBeInstanceOf(Medication::class)
            ->and($share->shareable->id)->toBe($medication->id);
    });

    it('correctly casts the status to an enum', function () {
        $share = ResourceShare::factory()->create(['status' => ShareStatusEnum::PENDING]);

        expect($share->status)->toBeInstanceOf(ShareStatusEnum::class)
            ->and($share->status)->toBe(ShareStatusEnum::PENDING);
    });

    it('has the correct array structure', function () {
        $share = ResourceShare::factory()->create()->refresh();
        $keys = array_keys($share->toArray());

        $expectedKeys = [
            'id',
            'owner_user_id',
            'shared_with_user_id',
            'shared_with_email',
            'shareable_type',
            'shareable_id',
            'status',
            'token',
            'expires_at',
            'created_at',
            'updated_at',
        ];

        expect($keys)->toHaveCount(count($expectedKeys))
            ->and($keys)->toEqualCanonicalizing($expectedKeys);
    });
});
