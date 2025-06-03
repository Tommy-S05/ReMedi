<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Prescription>
 */
final class PrescriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::inRandomOrder()->first()->id,
            'title' => $this->faker->optional()->sentence(3),
            'doctor_name' => $this->faker->optional()->name(),
            'prescription_date' => $this->faker->optional()->date(),
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }
}
