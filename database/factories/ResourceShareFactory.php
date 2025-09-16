<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ShareStatusEnum;
use App\Models\ResourceShare;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ResourceShare>
 */
final class ResourceShareFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<ResourceShare>
     */
    protected $model = ResourceShare::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $shareable = $this->faker->randomElement([
            \App\Models\Medication::class,
            \App\Models\Prescription::class,
        ]);

        return [
            'owner_user_id' => User::factory(),
            'shared_with_email' => $this->faker->unique()->safeEmail(),
            'shareable_id' => $shareable::factory(),
            'shareable_type' => $shareable,
            'status' => ShareStatusEnum::PENDING,
            'expires_at' => now()->addDays(7),
            'token' => Str::random(40),
        ];
    }
}
