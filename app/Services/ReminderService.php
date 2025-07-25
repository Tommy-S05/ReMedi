<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\MedicationScheduleFrequencyEnum;
use App\Models\MedicationSchedule;
use App\Models\User;
use App\Notifications\MedicationReminderNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Service class for handling medication reminder logic.
 */
final class ReminderService
{
    /**
     * Sends due medication reminders for a specific user at a given time.
     *
     * @param User $user The user to send reminders to.
     * @param Carbon $currentTime The current time to check for due reminders (server time).
     * @return void
     */
    public function sendRemindersForUser(User $user, Carbon $currentTime): void
    {
        $dueSchedulesData = $this->getDueSchedulesForUser($user, $currentTime);

        foreach ($dueSchedulesData as $data) {
            $user->notify(new MedicationReminderNotification(
                $data['schedule']->medication,
                $data['schedule'],
                $data['reminder_time']
            ));
        }
    }

    /**
     * Get all reminder occurrences for a user within a given date range.
     *
     * @param User $user
     * @param Carbon $from The start of the date range (in user's timezone).
     * @param Carbon $to The end of the date range (in user's timezone).
     * @return Collection
     */
    public function getOccurrencesForUser(User $user, Carbon $from, Carbon $to): Collection
    {
        $userTimezone = $user->timezone ?? config('app.timezone');
        $occurrences = collect();

        $activeSchedules = MedicationSchedule::query()
            ->whereHas('medication', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('is_active', true)
            ->where('start_date', '<=', $to->toDateString())
            ->where(function ($q) use ($from) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', $from->toDateString());
            })
            ->with('medication:id,name,dosage')
            ->get();

        /** @var MedicationSchedule $schedule */
        foreach ($activeSchedules as $schedule) {
            // Skip if the medication relationship is missing for some reason
            if (!$schedule->medication) {
                continue;
            }

            $firstDoseTime = Carbon::parse($schedule->start_date->toDateString() . ' ' . $schedule->time_to_take->toTimeString(), $userTimezone);

            $cursor = $firstDoseTime->copy();

            // If the first dose is after our range, skip this schedule
            if ($cursor->isAfter($to)) {
                continue;
            }

            // Fast-forward cursor to the start of our requested range if it starts long ago
            if ($cursor->isBefore($from)) {
                switch ($schedule->frequency_type->value) {
                    case 'daily':
                    case 'specific_days':
                        $cursor = $from->copy()->setTimeFromTimeString($schedule->time_to_take->toTimeString());
                        break;
                    case 'interval_in_days':
                        $diffInDays = $cursor->diffInDays($from);
                        $intervalsToSkip = floor($diffInDays / $schedule->interval_in_days);
                        $cursor->addDays($intervalsToSkip * $schedule->interval_in_days);
                        break;
                    case 'hourly_interval':
                        $diffInHours = $cursor->diffInHours($from);
                        $intervalsToSkip = floor($diffInHours / $schedule->interval_in_hours);
                        $cursor->addHours($intervalsToSkip * $schedule->interval_in_hours);
                        break;
                }
            }

            // Iterate and generate occurrences until we are past the end of the range
            while ($cursor->lessThanOrEqualTo($to)) {
                // Check if the current cursor time is a valid occurrence
                if ($this->isValidOccurrence($schedule, $cursor)) {
                    // Only add if it's within the requested range (after fast-forwarding)
                    if ($cursor->between($from, $to)) {
                        $occurrences->push([
                            'schedule' => $schedule,
                            'time' => $cursor->copy(),
                        ]);
                    }
                }

                // Advance the cursor to the next potential occurrence
                $this->advanceCursor($cursor, $schedule);

                // if for some reason cursor doesn't advance, prevent infinite loop
                if ($cursor->equalTo($occurrences->last()['time'] ?? $firstDoseTime)) {
                    Log::warning('Potential infinite loop detected in ReminderService.', ['schedule_id' => $schedule->id]);
                    break;
                }

                // Break if the schedule has a defined end date and we've passed it
                if ($schedule->end_date && $cursor->isAfter(Carbon::parse($schedule->end_date, $userTimezone)->endOfDay())) {
                    break;
                }
            }
        }

        return $occurrences->sortBy('time');
    }

    /**
     * Retrieves all medication schedules that are due for a user at a specific time.
     *
     * @param User $user The user to check.
     * @param Carbon $currentTime The current time (server time).
     * @return array<int, array{schedule: MedicationSchedule, reminder_time: Carbon}>
     */
    public function getDueSchedulesForUser(User $user, Carbon $currentTime): array
    {
        $userTimezone = $user->timezone ?? config('app.timezone');
        $currentTimeInUserTz = $currentTime->copy()->setTimezone($userTimezone);
        $activeSchedules = $this->getActiveSchedules($user, $currentTimeInUserTz);

        return $activeSchedules
            ->flatMap(fn (MedicationSchedule $schedule) => $this->calculateDueReminders($schedule, $currentTimeInUserTz, $userTimezone))
            ->unique(fn (array $item) => $item['schedule']->id . '-' . $item['reminder_time']->timestamp)
            ->values()
            ->all();
    }

    /**
     * Checks if a given time is a valid occurrence for a schedule (for specific days).
     */
    private function isValidOccurrence(MedicationSchedule $schedule, Carbon $time): bool
    {
        if ($schedule->frequency_type->value === 'specific_days') {
            return in_array($time->dayOfWeek, $schedule->days_of_week ?? []);
        }

        // For other types, the time is always valid if generated by the cursor logic.
        return true;
    }

    /**
     * Advances the cursor to the next potential dose time based on frequency.
     */
    private function advanceCursor(Carbon &$cursor, MedicationSchedule $schedule): void
    {
        switch ($schedule->frequency_type->value) {
            case 'daily':
                $cursor->addDay();
                break;
            case 'specific_days':
                // For specific days, we always just check the next day
                $cursor->addDay();
                break;
            case 'interval_in_days':
                $cursor->addDays($schedule->interval_in_days ?: 1);
                break;
            case 'hourly_interval':
                $cursor->addHours($schedule->interval_in_hours ?: 1);
                break;
            default:
                // Failsafe to prevent infinite loops on unknown types
                $cursor->addDay();
                break;
        }
    }

    /**
     * Get all active schedules for a user at a given time.
     *
     * @param User $user The user to check.
     * @param Carbon $currentTimeInUserTz The current time in the user's timezone.
     * @return Collection<int, MedicationSchedule>
     */
    private function getActiveSchedules(User $user, Carbon $currentTimeInUserTz): Collection
    {
        return $user->medications()
            ->with(['schedules' => function ($query) use ($currentTimeInUserTz) {
                $query->where('is_active', true)
                    ->where('start_date', '<=', $currentTimeInUserTz->toDateString())
                    ->where(function ($q) use ($currentTimeInUserTz) {
                        $q->whereNull('end_date')
                            ->orWhere('end_date', '>=', $currentTimeInUserTz->toDateString());
                    });
            }])
            ->get()
            ->pluck('schedules')
            ->flatten()
            ->filter();
    }

    /**
     * Calculate due reminders for a specific schedule.
     *
     * @param MedicationSchedule $schedule The medication schedule to check.
     * @param Carbon $currentTimeInUserTz The current time in the user's timezone.
     * @param string $userTimezone The user's timezone.
     * @return Collection<int, array{schedule: MedicationSchedule, reminder_time: Carbon}>
     */
    private function calculateDueReminders(MedicationSchedule $schedule, Carbon $currentTimeInUserTz, string $userTimezone): Collection
    {
        $timeToTakeParts = $this->parseTimeToTake($schedule->time_to_take);

        return match ($schedule->frequency_type) {
            // Caso 1: Frecuencia Diaria ('daily')
            MedicationScheduleFrequencyEnum::DAILY => $this->calculateDailyReminders($schedule, $currentTimeInUserTz, $userTimezone, $timeToTakeParts),

            // Caso 2: Días Específicos de la Semana ('specific_days')
            MedicationScheduleFrequencyEnum::SPECIFIC_DAYS => $this->calculateSpecificDaysReminders($schedule, $currentTimeInUserTz, $userTimezone, $timeToTakeParts),

            // Caso 3: Intervalo en Días ('interval_in_days')
            MedicationScheduleFrequencyEnum::INTERVAL_IN_DAYS => $this->calculateIntervalDaysReminders($schedule, $currentTimeInUserTz, $userTimezone, $timeToTakeParts),

            // Caso 4: Intervalo en Horas ('hourly_interval')
            MedicationScheduleFrequencyEnum::HOURLY_INTERVAL => $this->calculateHourlyIntervalReminders($schedule, $currentTimeInUserTz, $userTimezone, $timeToTakeParts),

            default => collect()
        };
    }

    /**
     * Parse time_to_take into hour and minute components.
     *
     * @param mixed $timeToTake The time to take, can be a Carbon instance or a string in HH:MM format.
     * @return array{hour: int, minute: int}
     */
    private function parseTimeToTake(mixed $timeToTake): array
    {
        $timeString = $timeToTake instanceof Carbon ? $timeToTake->toTimeString() : $timeToTake;
        [$hour, $minute] = explode(':', $timeString);

        return [
            'hour' => (int) $hour,
            'minute' => (int) $minute,
        ];
    }

    /**
     * Calculate daily reminders.
     *
     * @param MedicationSchedule $schedule The medication schedule to check.
     * @param Carbon $currentTimeInUserTz The current time in the user's timezone.
     * @param string $userTimezone The user's timezone.
     * @param array{hour: int, minute: int} $timeToTakeParts The time to take parts (hour and minute).
     * @return Collection<int, array{schedule: MedicationSchedule, reminder_time: Carbon}>
     */
    private function calculateDailyReminders(
        MedicationSchedule $schedule,
        Carbon $currentTimeInUserTz,
        string $userTimezone,
        array $timeToTakeParts
    ): Collection {
        $reminderTimeInUserTz = $currentTimeInUserTz->copy()->setTime($timeToTakeParts['hour'], $timeToTakeParts['minute']);

        if ($this->isTimeDue($reminderTimeInUserTz, $currentTimeInUserTz, $userTimezone)) {
            return collect([
                [
                    'schedule' => $schedule,
                    'reminder_time' => $reminderTimeInUserTz->setTimezone('UTC'),
                ],
            ]);
        }

        return collect();
    }

    /**
     * Calculate specific days reminders.
     *
     * @param MedicationSchedule $schedule The medication schedule to check.
     * @param Carbon $currentTimeInUserTz The current time in the user's timezone.
     * @param string $userTimezone The user's timezone.
     * @param array{hour: int, minute: int} $timeToTakeParts The time to take parts (hour and minute).
     * @return Collection<int, array{schedule: MedicationSchedule, reminder_time: Carbon}>
     */
    private function calculateSpecificDaysReminders(
        MedicationSchedule $schedule,
        Carbon $currentTimeInUserTz,
        string $userTimezone,
        array $timeToTakeParts
    ): Collection {
        if (!in_array($currentTimeInUserTz->dayOfWeek, $schedule->days_of_week ?? [])) {
            return collect();
        }

        $reminderTimeInUserTz = $currentTimeInUserTz->copy()->setTime($timeToTakeParts['hour'], $timeToTakeParts['minute']);

        if ($this->isTimeDue($reminderTimeInUserTz, $currentTimeInUserTz, $userTimezone)) {
            return collect([
                [
                    'schedule' => $schedule,
                    'reminder_time' => $reminderTimeInUserTz->setTimezone('UTC'),
                ],
            ]);
        }

        return collect();
    }

    /**
     * Calculate interval days reminders.
     *
     * @param MedicationSchedule $schedule The medication schedule to check.
     * @param Carbon $currentTimeInUserTz The current time in the user's timezone.
     * @param string $userTimezone The user's timezone.
     * @param array{hour: int, minute: int} $timeToTakeParts The time to take parts (hour and minute).
     * @return Collection<int, array{schedule: MedicationSchedule, reminder_time: Carbon}>
     */
    private function calculateIntervalDaysReminders(
        MedicationSchedule $schedule,
        Carbon $currentTimeInUserTz,
        string $userTimezone,
        array $timeToTakeParts
    ): Collection {
        if (!$schedule->interval_in_days || $schedule->interval_in_days <= 0) {
            return collect();
        }

        $startDate = Carbon::parse($schedule->start_date)->setTimezone($userTimezone)->startOfDay();
        $diffInDays = $startDate->diffInDays($currentTimeInUserTz->copy()->startOfDay());

        if ($diffInDays < 0 || $diffInDays % $schedule->interval_in_days !== 0) {
            return collect();
        }

        $reminderTimeInUserTz = $currentTimeInUserTz->copy()->setTime($timeToTakeParts['hour'], $timeToTakeParts['minute']);

        if ($this->isTimeDue($reminderTimeInUserTz, $currentTimeInUserTz, $userTimezone)) {
            return collect([
                [
                    'schedule' => $schedule,
                    'reminder_time' => $reminderTimeInUserTz->setTimezone('UTC'),
                ],
            ]);
        }

        return collect();
    }

    /**
     * Calculate hourly interval reminders.
     *
     * @param MedicationSchedule $schedule The medication schedule to check.
     * @param Carbon $currentTimeInUserTz The current time in the user's timezone.
     * @param string $userTimezone The user's timezone.
     * @param array{hour: int, minute: int} $timeToTakeParts The time to take parts (hour and minute).
     * @return Collection<int, array{schedule: MedicationSchedule, reminder_time: Carbon}>
     */
    private function calculateHourlyIntervalReminders(
        MedicationSchedule $schedule,
        Carbon $currentTimeInUserTz,
        string $userTimezone,
        array $timeToTakeParts
    ): Collection {
        if (!$schedule->interval_in_hours || $schedule->interval_in_hours <= 0) {
            return collect();
        }

        $scheduleStartDate = Carbon::parse($schedule->start_date)->setTimezone($userTimezone);
        $firstDoseTime = $scheduleStartDate->setTime($timeToTakeParts['hour'], $timeToTakeParts['minute']);
        $endDate = $schedule->end_date ? Carbon::parse($schedule->end_date)->setTimezone($userTimezone)->endOfDay() : null;

        $dueReminders = collect();
        $nextReminder = $firstDoseTime->copy();
        $maxIterations = 1000; // Prevent infinite loops
        $iterations = 0;

        while ($nextReminder->lessThanOrEqualTo($currentTimeInUserTz) && $iterations < $maxIterations) {
            $iterations++;

            // Check if reminder is within end date bounds
            if ($endDate && $nextReminder->greaterThan($endDate)) {
                break;
            }

            if ($this->isTimeDue($nextReminder, $currentTimeInUserTz, $userTimezone)) {
                $dueReminders->push([
                    'schedule' => $schedule,
                    'reminder_time' => $nextReminder->copy()->setTimezone('UTC'),
                ]);
            }

            $nextReminder->addHours($schedule->interval_in_hours);

            // Break if next reminder would be too far in the future
            if ($currentTimeInUserTz->diffInMinutes($nextReminder) > ($schedule->interval_in_hours * 60)) {
                break;
            }
        }

        return $dueReminders;
    }

    /**
     * Checks if a specific reminder time matches the current check time.
     *
     * @param Carbon $reminderTimeInUserTz The reminder time adjusted to the user's timezone.
     * @param Carbon $currentTimeInUserTz The current time in the user's timezone.
     * @param string $userTimezone The user's timezone.
     * @return bool
     */
    private function isTimeDue(Carbon $reminderTimeInUserTz, Carbon $currentTimeInUserTz, string $userTimezone): bool
    {
        // Ensure both times are in the user's timezone for comparison
        if ($reminderTimeInUserTz->getTimezone()->getName() !== $userTimezone) {
            $reminderTimeInUserTz = $reminderTimeInUserTz->setTimezone($userTimezone);
        }

        if ($currentTimeInUserTz->getTimezone()->getName() !== $userTimezone) {
            $currentTimeInUserTz = $currentTimeInUserTz->setTimezone($userTimezone);
        }

        return $reminderTimeInUserTz->format('Y-m-d H:i') === $currentTimeInUserTz->format('Y-m-d H:i');
    }
}
