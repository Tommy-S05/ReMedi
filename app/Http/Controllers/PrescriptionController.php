<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Http\Requests\StorePrescriptionRequest;
use App\Http\Requests\UpdatePrescriptionRequest;
use App\Services\MedicationService;
use App\Services\PrescriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Response;
use Throwable;

class PrescriptionController extends Controller
{
    public function __construct(
        protected readonly PrescriptionService $prescriptionService,
        protected readonly MedicationService $medicationService,
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(): Response
    {
        Gate::authorize('viewAny', Prescription::class);
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $prescriptions = $this->prescriptionService->getPrescriptionsForUser($user, true);
        return inertia('prescription/Index', [
            'prescriptions' => $prescriptions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(): Response
    {
        Gate::authorize('create', Prescription::class);
        // $userMedications = $this->medicationService->getMedicationsForUser(Auth::user())->map(fn($medication) => [
        //     'id' => $medication->id,
        //     'name' => $medication->name, // Y otros datos que quieras mostrar en el selector
        // ]);

        $userMedications = $this->medicationService->getMedicationSelectOptionsForUser(Auth::user());

        return inertia('prescription/Create', [
            'medications' => $userMedications,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StorePrescriptionRequest $request
     * @return RedirectResponse
     * @throws Throwable
     */
    public function store(StorePrescriptionRequest $request): RedirectResponse
    {
        
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $prescriptionData = $request->safe()->except(['medication_ids', 'medication_details']);
        $medicationDetails = $request->safe()->input('medication_details', []);

        // Si se envían medication_ids y no medication_details, transformarlos
        if (empty($medicationDetails) && !empty($request->input('medication_ids'))) {
            $medicationDetails = collect($request->input('medication_ids'))->map(fn($id) => ['medication_id' => $id])->all();
        }

        try {
            $this->prescriptionService->createPrescription($user, $prescriptionData, $medicationDetails);
            
            return redirect()->route('prescriptions.index')->with('success', __('messages.prescription_created_successfully'));
        } catch (Throwable $e) {
            return back()->with('error', __('messages.failed_to_create_prescription'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Prescription $prescription)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Prescription $prescription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePrescriptionRequest $request, Prescription $prescription)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prescription $prescription)
    {
        //
    }
}
