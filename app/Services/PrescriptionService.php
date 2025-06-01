<?php

namespace App\Services;

use App\Models\Prescription;
use App\Models\User;
use App\Models\Medication; // Importar
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Service class for handling prescription-related business logic.
 */
class PrescriptionService
{
    /**
     * Creates a new prescription for a user and optionally attaches medications.
     *
     * @param User $user The user who owns the prescription.
     * @param array<string, mixed> $prescriptionData Validated data for the prescription.
     * @param array<int, array<string, mixed>>|null $medicationDetails Optional.
     * @return Prescription The created prescription instance.
     * @throws \Throwable If any error occurs.
     */
    public function createPrescription(User $user, array $prescriptionData, ?array $medicationDetails = null): Prescription
    {
        try {
            return DB::transaction(function () use ($user, $prescriptionData, $medicationDetails) {
                /** @var Prescription $prescription */
                $prescription = $user->prescriptions()->create($prescriptionData);

                if (!empty($medicationDetails)) {
                    $this->syncMedicationsForPrescription($prescription, $medicationDetails);
                }

                return $prescription->load('medications');
            });
        } catch (Throwable $e) {
            Log::error(__METHOD__ . ' failed: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => $user->id,
                'data' => $prescriptionData
            ]);
            throw $e;
        }
    }

    /**
     * Updates an existing prescription and its medications.
     *
     * @param Prescription $prescription The prescription to update.
     * @param array<string, mixed> $prescriptionData Validated data for the prescription.
     * @param array<int, array<string, mixed>>|null $medicationDetails Optional. Array of medication details to sync.
     * @return Prescription The updated prescription instance.
     * @throws \Throwable If any error occurs.
     */
    public function updatePrescription(Prescription $prescription, array $prescriptionData, ?array $medicationDetails = null): Prescription
    {
        try {
            return DB::transaction(function () use ($prescription, $prescriptionData, $medicationDetails) {
                /** @var Prescription $prescription */
                $prescription->update($prescriptionData);

                if ($medicationDetails !== null) {
                    $this->syncMedicationsForPrescription($prescription, $medicationDetails);
                }

                return $prescription->load('medications');
            });
        } catch (Throwable $e) {
            Log::error(__METHOD__ . ' failed: ' . $e->getMessage(), [
                'exception' => $e,
                'prescription_id' => $prescription->id,
                'data' => $prescriptionData
            ]);
            throw $e;
        }
    }

    /**
     * Deletes a given prescription.
     * Associated medications in the pivot table will be detached due to onDelete('cascade')
     * or handled by Eloquent's detach on model delete if relationships are set up that way.
     * (Nota: `onDelete('cascade')` en la tabla pivote es para cuando se borra un Medication o Prescription, no para el detach).
     * Eloquent se encarga de detach al borrar un modelo con relación BelongsToMany.
     *
     * @param Prescription $prescription The prescription to delete.
     * @return bool True on success.
     * @throws \Throwable If any error occurs.
     */
    public function deletePrescription(Prescription $prescription): bool
    {
        try {
            // La autorización ya debería haber ocurrido en el controlador.
            // Al eliminar una prescription, Eloquent automáticamente llamará a detach()
            // para las relaciones BelongsToMany, eliminando los registros de la tabla pivote.
            return $prescription->delete();
        } catch (Throwable $e) {
            Log::error(__METHOD__ . ' failed: ' . $e->getMessage(), [
                'exception' => $e,
                'prescription_id' => $prescription->id
            ]);
            throw $e;
        }
    }

    /**
     * Retrieves all prescriptions for a given user, optionally with their medications.
     *
     * @param User $user The user whose prescriptions to retrieve.
     * @param bool $withMedications Whether to eager load medications.
     * @return \Illuminate\Support\Collection<int, Prescription>
     */
    public function getPrescriptionsForUser(User $user, bool $withMedications = true): Collection
    {
        $query = $user->prescriptions()->latest('prescription_date')->latest(); // Ordenar
        if ($withMedications) {
            $query->with('medications');
        }
        return $query->get();
    }

    /**
     * Syncs medications for a given prescription.
     *
     * @param Prescription $prescription
     * @param array<int, array<string, mixed>> $medicationDetails Each item: ['medication_id' => id, 'dosage_on_prescription' => ..., etc.]
     * @return void
     */
    protected function syncMedicationsForPrescription(Prescription $prescription, array $medicationDetails): void
    {
        $syncData = [];
        foreach ($medicationDetails as $detail) {
            // Solo incluir medication_id si es un medicamento del usuario
            /** @var User $user */
            $medication = Medication::where('id', $detail['medication_id'])
                ->where('user_id', Auth::user()->id)
                ->first();

            if ($medication) {
                $syncData[$detail['medication_id']] = [
                    'dosage_on_prescription' => $detail['dosage_on_prescription'] ?? null,
                    'quantity_prescribed' => $detail['quantity_prescribed'] ?? null,
                    'instructions_on_prescription' => $detail['instructions_on_prescription'] ?? null,
                ];
            } else {
                Log::warning('Attempted to sync non-existent or unauthorized medication to prescription.', [
                    'prescription_id' => $prescription->id,
                    'attempted_medication_id' => $detail['medication_id']
                ]);
            }
        }
        $prescription->medications()->sync($syncData);
    }
}
