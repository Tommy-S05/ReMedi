<?php

namespace App\Http\Requests;

use App\Enums\MedicationScheduleFrequencyEnum;
use App\Enums\MedicationTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreMedicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user() != null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', Rule::enum(MedicationTypeEnum::class)],
            'dosage' => ['nullable', 'string', 'max:100'],
            'strength' => ['nullable', 'string', 'max:100'],
            'quantity' => ['nullable', 'integer', 'min:0'],
            'instructions' => ['nullable', 'string'],

            'schedules' => ['required', 'array', 'min:1'],
            'schedules.*.time_to_take' => ['required', 'date_format:H:i'],
            'schedules.*.frequency_type' => ['required', Rule::enum(MedicationScheduleFrequencyEnum::class)],
            'schedules.*.days_of_week' => [
                'nullable',
                'array',
                'required_if:schedules.*.frequency_type,' . MedicationScheduleFrequencyEnum::SPECIFIC_DAYS->value,
            ],
            'schedules.*.days_of_week.*' => ['integer', 'min:0', 'max:6'],

            'schedules.*.interval_in_days' => [
                'nullable',
                'integer',
                'min:1',
                'required_if:schedules.*.frequency_type,' . MedicationScheduleFrequencyEnum::INTERVAL_IN_DAYS->value,
            ],

            'schedules.*.interval_in_hours' => [
                'nullable',
                'integer',
                'min:1',
                'required_if:schedules.*.frequency_type,' . MedicationScheduleFrequencyEnum::HOURLY_INTERVAL->value,
            ],
            'schedules.*.start_date' => ['required', 'date'],
            'schedules.*.end_date' => ['nullable', 'date', 'after_or_equal:schedules.*.start_date'],
        ];
    }
}
