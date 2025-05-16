<?php

namespace App\Http\Controllers;

use App\Enums\MedicationScheduleFrequencyEnum;
use App\Enums\MedicationTypeEnum;
use App\Models\Medication;
use App\Http\Requests\StoreMedicationRequest;
use App\Http\Requests\UpdateMedicationRequest;
use App\Models\User;
use App\Services\MedicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class MedicationController extends Controller
{
    public function __construct(
        private readonly MedicationService $medicationService
    ) {}

    /**
     * Display a listing of the user's medications.
     * Will include all schedules associated with each medication.
     *
     * @return Response
     */
    public function index(): Response
    {
        /** @var User $user */
        $user = Auth::user();

        $medications = $this->medicationService->getMedicationsForUser($user);

        return Inertia::render('medications/Index', [
            'medications' => $medications,
            // pero podrían ser útiles si tienes filtros en el frontend que usen estos valores.
            'medicationTypeLabels' => MedicationTypeEnum::forSelect(),
            'frequencyTypeLabels' => MedicationScheduleFrequencyEnum::forSelect(),
        ]);
    }

    /**
     * Show the form for creating a new medication.
     *
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('medications/Create', [
            'medicationTypes' => MedicationTypeEnum::forSelect(),
            'frequencyOptions' => MedicationScheduleFrequencyEnum::forSelect(),
        ]);
    }

    /**
     * Store a newly created medication in storage.
     *
     * @param StoreMedicationRequest $request
     * @return RedirectResponse
     */
    public function store(StoreMedicationRequest $request): RedirectResponse
    {
        $validatedMedicationData = $request->safe()->except('schedules');
        $validatedSchedulesData = $request->safe()->input('schedules', []);

        try {
            /** @var User $user */
            $user = Auth::user();

            $this->medicationService->createMedication(
                $user,
                $validatedMedicationData,
                $validatedSchedulesData
            );

            return to_route('medications.index');
        } catch (\Throwable $e) {
            return back()->with('error', __('messages.failed_to_add_medication'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Medication $medication)
    {
        //
    }

    /**
     * Show the form for editing the specified medication.
     *
     * @param  Medication  $medication
     * @return Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Medication $medication): Response
    {
        Gate::authorize('update', $medication);
        $medication->load('schedules');

        return Inertia::render('medications/Edit', [
            'medication' => $medication,
            'medicationTypes' => MedicationTypeEnum::forSelect(),
            'frequencyOptions' => MedicationScheduleFrequencyEnum::forSelect(),
        ]);
    }

    /**
     * Update the specified medication in storage.
     *
     * @param UpdateMedicationRequest $request
     * @param Medication $medication
     * @return RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateMedicationRequest $request, Medication $medication): RedirectResponse
    {
        Gate::authorize('update', $medication);
        $validatedMedicationData = $request->safe()->except('schedules');
        $validatedSchedulesData = $request->safe()->input('schedules', []);
        try {
            $this->medicationService->updateMedication(
                $medication,
                $validatedMedicationData,
                $validatedSchedulesData
            );
            return to_route('medications.index');
        } catch (\Throwable $e) {
            Log::error('MedicationController::update failed: ' . $e->getMessage(), [
                'medication_id' => $medication->id,
                'exception' => $e
            ]);
            return back()->with('error', __('messages.failed_to_update_medication'));
        }
    }

    /**
     * Remove the specified medication from storage.
     *
     * @param  Medication  $medication
     * @return RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Medication $medication): RedirectResponse
    {
        Gate::authorize('delete', $medication);
        try {
            $this->medicationService->deleteMedication($medication);

            return back()->with('success', __('messages.medication_deleted_successfully'));
        } catch (\Throwable $e) {
            return back()
                ->with('error', __('messages.failed_to_delete_medication'));
        }
    }
}
