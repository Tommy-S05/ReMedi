<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\MedicationLogStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

/**
 * Form request for logging a medication take action.
 *
 * @property-read string $notification_id
 * @property-read string $status
 */
final class LogMedicationTakeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // La autorización real (si la notificación pertenece al usuario)
        // se hará en el servicio para mayor seguridad, pero como mínimo,
        // el usuario debe estar autenticado.
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'notification_id' => ['required', 'string', 'exists:notifications,id'],
            'status' => ['required', new Enum(MedicationLogStatusEnum::class)],
            // Rule::in([MedicationLogStatusEnum::TAKEN->value, MedicationLogStatusEnum::SKIPPED->value])
        ];
    }
}
