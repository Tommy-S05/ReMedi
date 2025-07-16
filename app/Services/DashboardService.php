<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\MedicationLogStatusEnum;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Service class for handling dashboard-related business logic.
 */
final readonly class DashboardService
{
    public function __construct(private ReminderService $reminderService) {}

    /**
     * Gathers all necessary data for the user's dashboard.
     *
     * @param User $user
     * @return array<string, mixed>
     */
    /**
     * Gathers all necessary data for the user's dashboard, using a cache layer.
     *
     * @param User $user
     * @return array<string, mixed>
     */
    public function getDataForUser(User $user): array
    {
        return $this->buildDashboardData($user);
        // Define a unique cache key for this user
        // $cacheKey = "dashboard:user:{$user->id}";
        // return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($user) {
        //     return $this->buildDashboardData($user);
        // });
    }

    /**
     * The core logic to build the dashboard data.
     *
     * @param User $user
     * @return array<string, mixed>
     */
    public function buildDashboardData(User $user): array
    {
        $userTimezone = $user->timezone ?? config('app.timezone');
        $now = Carbon::now($userTimezone);

        $rangeStart = $now->copy()->subDays(6)->startOfDay();
        $rangeEnd = $now->copy()->addDay()->endOfDay();

        // Get all occurrences in this wide range with a SINGLE call.
        $allOccurrences = $this->reminderService->getOccurrencesForUser($user, $rangeStart, $rangeEnd);

        // 1. Calculate Stats
        $stats = $this->getDashboardStats($user, $now, $allOccurrences);

        // 2. Get Today's Reminders
        $remindersForToday = $this->getRemindersForToday($user, $now, $allOccurrences);

        return [
            'stats' => $stats,
            'remindersForToday' => $remindersForToday,
        ];
    }

    /**
     * Calculates the key statistics by filtering the master occurrences collection.
     */
    private function getDashboardStats(User $user, Carbon $now, Collection $allOccurrences): array
    {
        // 1. Próxima Dosis: Filter for occurrences from now onwards.
        $nextDose = $allOccurrences->first(fn ($occ) => $occ['time']->isFuture());

        // 2. Medicamentos Activos
        $activeMedicationsCount = $user->medications()
            ->whereHas('schedules', fn ($q) => $q->where('is_active', true))
            ->count();

        // 3. Adherencia: Use the master collection.
        $adherence = $this->calculateAdherence($user, $now, $allOccurrences);

        return [
            'nextDose' => $nextDose ? [
                'medication_name' => $nextDose['schedule']->medication->name,
                'time' => $nextDose['time']->format('h:i A'),
            ] : null,
            'activeMedicationsCount' => $activeMedicationsCount,
            'adherencePercentage' => $adherence,
        ];
    }

    /**
     * Calculates adherence by filtering the master occurrences collection.
     */
    private function calculateAdherence(User $user, Carbon $now, Collection $allOccurrences): int
    {
        $startDate = $now->copy()->subDays(6)->startOfDay();
        $endDate = $now->copy()->endOfDay();

        // Filter master collection for doses scheduled in the last 7 days.
        $totalScheduled = $allOccurrences->filter(
            fn ($occ) => $occ['time']->between($startDate, $endDate)
        )->count();

        if ($totalScheduled === 0) {
            return 100;
        }

        $totalTaken = $user->medicationTakeLogs()
            ->where('status', MedicationLogStatusEnum::TAKEN)
            ->whereBetween('scheduled_for', [$startDate->copy()->utc(), $endDate->copy()->utc()])
            ->count();

        return (int) round(($totalTaken / $totalScheduled) * 100);
    }

    /**
     * Gets today's reminders by filtering the master occurrences collection.
     */
    private function getRemindersForToday(User $user, Carbon $now, Collection $allOccurrences): Collection
    {
        $startOfDay = $now->copy()->startOfDay();
        $endOfDay = $now->copy()->endOfDay();

        $todaysOccurrences = $allOccurrences->filter(
            fn ($occ) => $occ['time']->between($startOfDay, $endOfDay)
        );

        if ($todaysOccurrences->isEmpty()) {
            return collect();
        }

        $todaysLogs = $user->medicationTakeLogs()
            ->whereBetween('scheduled_for', [$startOfDay->copy()->utc(), $endOfDay->copy()->utc()])
            ->get()
            ->keyBy(fn ($log) => $log->medication_schedule_id . '-' . $log->scheduled_for->timestamp);

        return $todaysOccurrences->map(function ($occurrence) use ($todaysLogs, $now) {
            $key = $occurrence['schedule']->id . '-' . $occurrence['time']->timestamp;
            $log = $todaysLogs->get($key);

            return [
                'medication_name' => $occurrence['schedule']->medication->name,
                'dosage' => $occurrence['schedule']->medication->dosage,
                'time' => $occurrence['time']->format('h:i A'),
                'is_past' => $occurrence['time']->isPast($now),
                'status' => $log?->status->value,
            ];
        })->values();
    }
}
