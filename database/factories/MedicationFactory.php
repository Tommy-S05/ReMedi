<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Medication>
 */
class MedicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'type' => $this->faker->randomElement(\App\Enums\MedicationTypeEnum::cases()),
            'dosage' => $this->faker->numerify('# ##'),
            'strength' => $this->faker->randomElement(['10mg', '20mg', '50mg', '100mg', null]),
            'quantity' => $this->faker->optional()->numberBetween(10, 100),
            'instructions' => $this->faker->sentence(),
            'user_id' => \App\Models\User::inRandomOrder()->first()->id,
        ];
    }
}
