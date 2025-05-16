<?php

namespace App\Services;

use App\Enums\MedicationScheduleFrequencyEnum;
use App\Enums\MedicationTypeEnum;
use App\Models\Medication;
use App\Models\MedicationSchedule;
use App\Models\User;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Service class for handling medication-related business logic.
 */
class MedicationService
{
    /**
     * Retrieves all medications for a given user, including their schedules,
     * and prepares them for display with translated enum labels.
     *
     * @param User $user The user whose medications to retrieve.
     * @return Collection<int, Medication>
     */
    public function getMedicationsForUser(User $user): Collection
    {
        $medications = $user->medications()
            ->with([
                'schedules' => function ($query) {
                    $query->orderBy('time_to_take', 'asc')->orderBy('start_date', 'asc');
                }
            ])
            ->latest()
            ->get();

        return $medications;
    }

    /**
     * Get the human-readable label for a MedicationTypeEnum.
     *
     * @param MedicationTypeEnum $typeEnum
     * @return string
     */
    public function getMedicationTypeLabel(MedicationTypeEnum $typeEnum): string
    {
        return $typeEnum->label();
    }

    /**
     * Get the human-readable label for a MedicationScheduleFrequencyEnum.
     *
     * @param MedicationScheduleFrequencyEnum $frequencyEnum
     * @return string
     */
    public function getMedicationScheduleFrequencyLabel(MedicationScheduleFrequencyEnum $frequencyEnum): string
    {
        return $frequencyEnum->label();
    }

    /**
     * Creates a new medication with its associated schedules for a user.
     *
     * @param User $user The user who owns the medication.
     * @param array<string, mixed> $medicationData Validated data for the medication.
     * Expected keys: 'name', 'type' (MedicationTypeEnum value), 'dosage', etc.
     * @param array<int, array<string, mixed>> $schedulesData Array of validated schedule data.
     * Each schedule array expects keys like: 'time_to_take',
     * 'frequency_type' (MedicationScheduleFrequencyEnum value), 'start_date', etc.
     * @return Medication The created medication instance.
     * @throws \Throwable If any error occurs during the creation process.
     */
    public function createMedication(User $user, array $medicationData, array $schedulesData): Medication
    {
        try {
            DB::beginTransaction();

            /** @var Medication $medication */
            $medication = $user->medications()->create($medicationData);

            // Preparamos un array de schedules para crear en lote
            $schedulesPayload = collect($schedulesData)->map(function (array $scheduleInput) {
                $frequencyType = $scheduleInput['frequency_type'] instanceof MedicationScheduleFrequencyEnum
                    ? $scheduleInput['frequency_type']
                    : MedicationScheduleFrequencyEnum::tryFrom($scheduleInput['frequency_type']);

                return [
                    'time_to_take'       => $scheduleInput['time_to_take'],
                    'frequency_type'     => $frequencyType,
                    'start_date'         => $scheduleInput['start_date'],
                    'end_date'           => $scheduleInput['end_date'] ?? null,
                    'is_active'          => $scheduleInput['is_active'] ?? true,
                    'days_of_week'       => $frequencyType === MedicationScheduleFrequencyEnum::SPECIFIC_DAYS
                        ? ($scheduleInput['days_of_week'] ?? null)
                        : null,
                    'interval_in_days'   => $frequencyType === MedicationScheduleFrequencyEnum::INTERVAL_IN_DAYS
                        ? ($scheduleInput['interval_in_days'] ?? null)
                        : null,
                    'interval_in_hours'  => $frequencyType === MedicationScheduleFrequencyEnum::HOURLY_INTERVAL
                        ? ($scheduleInput['interval_in_hours'] ?? null)
                        : null,
                ];
            })->toArray();

            $medication->schedules()->createMany($schedulesPayload);

            DB::commit();

            return $medication;
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('MedicationService::createMedication failed: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => $user->id,
                'medication_data' => $medicationData,
                'schedules_data' => $schedulesData,
            ]);
            throw $e;
        }
    }

    /**
     * Deletes a given medication and its associated schedules.
     * The schedules will be deleted automatically due to onDelete('cascade')
     * defined in the migration for the foreign key.
     *
     * @param Medication $medication The medication instance to delete.
     * @return bool True on success, false or throws exception on failure.
     * @throws \Throwable If any error occurs during the deletion process.
     */
    public function deleteMedication(Medication $medication): bool
    {
        try {
            return DB::transaction(function () use ($medication) {
                return $medication->delete();
            });
        } catch (Throwable $e) {
            Log::error('MedicationService::deleteMedication failed: ' . $e->getMessage(), [
                'medication_id' => $medication->id,
                'exception' => $e
            ]);
            throw $e;
        }
    }

    /**
     * Updates an existing medication and its associated schedules.
     * Schedules not present in $schedulesData will be deleted.
     * Schedules in $schedulesData without an ID will be created.
     * Schedules in $schedulesData with an ID will be updated.
     *
     * @param Medication $medication The medication instance to update.
     * @param array<string, mixed> $medicationData Validated data for the medication.
     * @param array<int, array<string, mixed>> $schedulesData Array of validated schedule data.
     * @return Medication The updated medication instance.
     * @throws \Throwable If any error occurs during the update process.
     */
    public function updateMedication(Medication $medication, array $medicationData, array $schedulesData): Medication
    {
        try {
            DB::beginTransaction();

            $medication->update($medicationData);

            if (! empty($schedulesData)) {
                $existingScheduleIds = $medication->schedules->pluck('id')->toArray();
                $incomingScheduleIds = [];

                foreach ($schedulesData as $scheduleInput) {
                    $frequencyType = $scheduleInput['frequency_type'] instanceof MedicationScheduleFrequencyEnum
                        ? $scheduleInput['frequency_type']
                        : MedicationScheduleFrequencyEnum::tryFrom($scheduleInput['frequency_type']);

                    $scheduleToSave = [
                        'time_to_take'       => $scheduleInput['time_to_take'],
                        'frequency_type'     => $frequencyType,
                        'start_date'         => $scheduleInput['start_date'],
                        'end_date'           => $scheduleInput['end_date'] ?? null,
                        'is_active'          => $scheduleInput['is_active'] ?? true,
                        'days_of_week'       => $frequencyType === MedicationScheduleFrequencyEnum::SPECIFIC_DAYS
                            ? ($scheduleInput['days_of_week'] ?? null)
                            : null,
                        'interval_in_days'   => $frequencyType === MedicationScheduleFrequencyEnum::INTERVAL_IN_DAYS
                            ? ($scheduleInput['interval_in_days'] ?? null)
                            : null,
                        'interval_in_hours'  => $frequencyType === MedicationScheduleFrequencyEnum::HOURLY_INTERVAL
                            ? ($scheduleInput['interval_in_hours'] ?? null)
                            : null,
                    ];

                    // Update existing schedule or create new one
                    if (isset($scheduleInput['id']) && !empty($scheduleInput['id'])) {
                        // Actualizar horario existente
                        $schedule = $medication->schedules()->find($scheduleInput['id']);
                        if ($schedule) {
                            $schedule->update($scheduleToSave);
                            $incomingScheduleIds[] = $schedule->id;
                        }
                    } else {
                        // Crear nuevo horario
                        $newSchedule = $medication->schedules()->create($scheduleToSave);
                        $incomingScheduleIds[] = $newSchedule->id;
                    }
                }

                // Eliminar horarios que ya no están en la lista
                $schedulesToDelete = array_diff($existingScheduleIds, $incomingScheduleIds);
                if (!empty($schedulesToDelete)) {
                    MedicationSchedule::destroy($schedulesToDelete);
                } else {
                    // $medication->schedules()->delete();
                }
            }

            DB::commit();

            // Recargar con los horarios actualizados
            return $medication->fresh(['schedules']);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('MedicationService::updateMedication failed: ' . $e->getMessage(), [
                'medication_id' => $medication->id,
                'exception' => $e,
                'medication_data' => $medicationData,
                'schedules_data' => $schedulesData,
            ]);
            throw $e;
        }
    }
}
