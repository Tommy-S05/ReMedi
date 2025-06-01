<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdatePrescriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $prescription = $this->route('prescription');
        return $prescription && $this->user()->can('update', $prescription);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'doctor_name' => ['nullable', 'string', 'max:255'],
            'prescription_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'medication_ids' => ['sometimes', 'array'],
            'medication_ids.*' => [
                'integer',
                Rule::exists('medications', 'id')
                    ->where(fn($query) => $query->where('user_id', Auth::id())),
            ],
            'medication_details' => ['sometimes', 'array'],
            'medication_details.*.medication_id' => [
                'required_with:medication_details',
                'integer',
                Rule::exists('medications', 'id')
                    ->where(fn($query) => $query->where('user_id', Auth::id())),
            ],
            'medication_details.*.dosage_on_prescription' => ['nullable', 'string', 'max:255'],
            'medication_details.*.quantity_prescribed' => ['nullable', 'string', 'max:255'],
            'medication_details.*.instructions_on_prescription' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'medication_ids.*.exists' => __('validation.medication_not_found_for_user'),
            'medication_details.*.medication_id.exists' => __('validation.medication_not_found_for_user'),
        ];
    }
}
