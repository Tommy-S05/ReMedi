<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Handles business logic related to calendar events.
 */
final readonly class CalendarService
{
    public function __construct(private ReminderService $reminderService) {}

    /**
     * Get all formatted calendar events for a user within a given date range.
     *
     * @param User $user
     * @param Carbon $startRange The start of the date range (in user's timezone).
     * @param Carbon $endRange The end of the date range (in user's timezone).
     * @return Collection
     */
    public function getEventsForRange(User $user, Carbon $startRange, Carbon $endRange): Collection
    {
        // 1. Get all raw reminder occurrences from the ReminderService.
        $occurrences = $this->reminderService->getOccurrencesForUser($user, $startRange, $endRange);

        // 2. Transform these occurrences into the event format that FullCalendar expects.
        return $occurrences->map(function ($occurrence) {
            return [
                'id' => $occurrence['schedule']->id . '-' . $occurrence['time']->timestamp,
                'title' => $occurrence['schedule']->medication->name,
                'start' => $occurrence['time']->toISOString(), // Use ISO 8601 format
                'allDay' => false, // Doses happen at a specific time
                'extendedProps' => [
                    'dosage' => $occurrence['schedule']->medication->dosage,
                    'timeFormatted' => $occurrence['time']->format('h:i A'),
                ],
                // Add custom class for styling
                'className' => 'remedi-event',
            ];
        })->values();
    }
}
