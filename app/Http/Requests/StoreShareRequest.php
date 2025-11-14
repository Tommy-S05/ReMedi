<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Services\ShareableTypeRegistry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

final class StoreShareRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            'email' => ['required', 'email'],
            'shareable_type' => [
                'required',
                'string',
                Rule::in(ShareableTypeRegistry::getValidTypes()),
            ],
            'shareable_id' => ['required', 'integer'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param Validator $validator
     * @return void
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $type = (string) $this->input('shareable_type');
            if (ShareableTypeRegistry::isValidType($type)) {
                $modelClass = ShareableTypeRegistry::getModelClass($type);

                $exists = $modelClass::query()
                    ->where('id', $this->input('shareable_id'))
                    ->exists();

                if (!$exists) {
                    $validator->errors()->add('shareable_id', 'The selected shareable_id is invalid for the given type.');
                }
            }
        });
    }
}
