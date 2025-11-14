<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\MedicationLogStatusEnum;
use App\Models\Medication;
use App\Models\MedicationSchedule;
use App\Models\MedicationTakeLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<MedicationTakeLog>
 */
final class MedicationTakeLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<MedicationTakeLog>
     */
    protected $model = MedicationTakeLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(MedicationLogStatusEnum::cases());
        $scheduledFor = Carbon::parse($this->faker->dateTimeThisMonth());

        $actionTakenAt = null;
        if ($status === MedicationLogStatusEnum::TAKEN) {
            $actionTakenAt = $scheduledFor->clone()->addMinutes($this->faker->numberBetween(1, 60));
        }

        return [
            'user_id' => User::factory(),
            'medication_id' => Medication::factory(),
            'medication_schedule_id' => MedicationSchedule::factory(),
            'status' => $status,
            'scheduled_for' => $scheduledFor,
            'action_taken_at' => $actionTakenAt,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
