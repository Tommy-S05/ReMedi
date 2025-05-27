<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicationSchedule>
 */
class MedicationScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $frequencyType = $this->faker->randomElement(\App\Enums\MedicationScheduleFrequencyEnum::cases());
        $attributes = [
            'medication_id' => \App\Models\Medication::factory(), // Asume que tienes un MedicationFactory
            'time_to_take' => $this->faker->time('H:i'),
            'frequency_type' => $frequencyType,
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->optional(0.7)->dateTimeBetween('+1 week', '+1 year'), // 70% de las veces tendrá fecha de fin
            'is_active' => $this->faker->boolean(90), // 90% de las veces estará activo
        ];

        switch ($frequencyType) {
            case \App\Enums\MedicationScheduleFrequencyEnum::SPECIFIC_DAYS:
                $attributes['days_of_week'] = $this->faker->randomElements([0, 1, 2, 3, 4, 5, 6], $this->faker->numberBetween(1, 7));
                break;
            case \App\Enums\MedicationScheduleFrequencyEnum::INTERVAL_IN_DAYS:
                $attributes['interval_in_days'] = $this->faker->numberBetween(1, 30);
                break;
            case \App\Enums\MedicationScheduleFrequencyEnum::HOURLY_INTERVAL:
                $attributes['interval_in_hours'] = $this->faker->randomElement([1, 2, 3, 4, 6, 8, 12, 24]);
                break;
        }

        return $attributes;
    }
}
