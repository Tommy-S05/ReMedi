<?php

namespace App\Enums;

/**
 * Enum for medication schedule frequencies.
 */
enum MedicationScheduleFrequencyEnum: string
{
    case DAILY = 'daily';
    case SPECIFIC_DAYS = 'specific_days';
    case INTERVAL_IN_DAYS = 'interval_in_days';
    case HOURLY_INTERVAL = 'hourly_interval';

    /**
     * Get the human-readable label for the frequency.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::DAILY => 'Daily',
            self::SPECIFIC_DAYS => 'Specific days of the week',
            self::INTERVAL_IN_DAYS => 'Every X days',
            self::HOURLY_INTERVAL => 'Every X hours',
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
