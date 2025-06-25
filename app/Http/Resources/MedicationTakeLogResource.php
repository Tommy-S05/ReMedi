<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\MedicationTakeLog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property MedicationTakeLog $resource
 */
final class MedicationTakeLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'status' => $this->resource->status->value,
            'status_label' => $this->resource->status->label(),
            'scheduled_for_formatted' => $this->resource->scheduled_for_formatted,
            'action_taken_at_formatted' => $this->resource->action_taken_at_formatted,
            'medication' => [
                'id' => $this->whenLoaded('medication', fn () => $this->resource->medication->id),
                'name' => $this->whenLoaded('medication', fn () => $this->resource->medication->name),
            ],
        ];
    }
}
