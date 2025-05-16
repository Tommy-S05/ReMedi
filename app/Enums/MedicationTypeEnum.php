<?php

namespace App\Enums;

/**
 * Enum for medication types.
 */
enum MedicationTypeEnum: string
{
    case PILL = 'pill';
    case CAPSULE = 'capsule';
    case SYRUP = 'syrup';
    case INJECTION = 'injection';
    case INHALER = 'inhaler';
    case DROPS = 'drops';
    case OINTMENT = 'ointment';
    case PATCH = 'patch';
    case OTHER = 'other';

    /**
     * Get the human-readable label for the medication type.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::PILL => 'Pill / Tablet',
            self::CAPSULE => 'Capsule',
            self::SYRUP => 'Syrup / Liquid',
            self::INJECTION => 'Injection',
            self::INHALER => 'Inhaler',
            self::DROPS => 'Drops',
            self::OINTMENT => 'Ointment / Cream',
            self::PATCH => 'Patch',
            self::OTHER => 'Other',
        };
    }

    /**
     * Get all cases formatted for a select input.
     *
     * @return array<int, array{value: string, label: string}>
     */
    public static function forSelect(): array
    {
        return collect(self::cases())->map(function($case) {
            return [
                'value' => $case->value,
                'label' => $case->label(),
            ];
        })->all();
    }
}
