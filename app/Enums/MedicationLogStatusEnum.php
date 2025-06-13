<?php

declare(strict_types=1);

namespace App\Enums;

enum MedicationLogStatusEnum: string
{
    case TAKEN = 'taken';
    case SKIPPED = 'skipped';
    case MISSED = 'missed'; // El sistema marcó el medicamento como perdido (pasó el tiempo sin acción).

    /**
     * Get the human-readable label for the status.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::TAKEN => 'Taken',
            self::SKIPPED => 'Skipped',
            self::MISSED => 'Missed',
        };
    }
}
