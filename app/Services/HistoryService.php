<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Resources\MedicationTakeLogResource;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Service class for handling history-related business logic.
 */
final class HistoryService
{
    /**
     * Retrieves the paginated and grouped medication take history for a user.
     *
     * @param User $user The user whose history to retrieve.
     * @param int $perPage Number of items per page.
     * @param int $page The page number to retrieve.
     * @return array{history: Collection, pagination_meta: object, pagination_links: object}
     */
    public function getPaginatedGroupedHistory(User $user, int $perPage = 10, int $page = 1): array
    {
        $userTimezone = $user->timezone ?? config('app.timezone');

        // 1. Obtener los datos paginados
        $logsPaginator = $this->getPaginatedHistory($user, $perPage, $page);

        // 2. Transformar los datos usando el Resource
        $transformedLogs = MedicationTakeLogResource::collection($logsPaginator);

        // 3. Agrupar por fecha manteniendo el orden
        $groupedLogs = $this->groupLogsByDate($transformedLogs->collection, $userTimezone);

        // 4. Formatear las claves de fecha del grupo
        $formattedGroupedLogs = $this->formatDateKeys($groupedLogs, $userTimezone);

        // 5. Extraer metadata de paginación
        $paginationData = $this->extractPaginationData($transformedLogs);

        return [
            'history' => $formattedGroupedLogs,
            'pagination_meta' => $paginationData['meta'],
            'pagination_links' => $paginationData['links'],
        ];
    }

    /**
     * Retrieves the paginated medication take history for a user.
     *
     * @param User $user The user whose history to retrieve.
     * @param int $perPage Number of items per page.
     * @param int $page The page number to retrieve.
     * @return LengthAwarePaginator
     */
    public function getPaginatedHistory(User $user, int $perPage = 10, int $page = 1): LengthAwarePaginator
    {
        return $user->medicationTakeLogs()
            ->with('medication:id,name')
            ->orderBy('scheduled_for', 'desc')
            ->paginate(
                perPage: $perPage,
                page: $page,
            );
    }

    /**
     * Gets the per-page value from the request with validation.
     *
     * @param Request $request
     * @return int
     */
    public function getPerPageFromRequest(Request $request): int
    {
        $perPage = $request->integer('per_page', 30);

        // Limitar el rango de elementos por página
        return min(max($perPage, 10), 100);
    }

    /**
     * Get the page number from the request with validation.
     *
     * @param Request $request
     * @return int
     */
    public function getPageFromRequest(Request $request): int
    {
        $page = $request->integer('page', 1);

        // Asegurarse de que la página sea al menos 1
        return max($page, 1);
    }

    /**
     * Groups logs by date while maintaining chronological order.
     *
     * @param Collection $logs
     * @param string $userTimezone
     * @return Collection
     */
    private function groupLogsByDate(Collection $logs, string $userTimezone): Collection
    {
        return $logs->groupBy(function ($log) use ($userTimezone) {
            $scheduledFor = $log->resource->scheduled_for;

            return Carbon::parse($scheduledFor)
                ->setTimezone($userTimezone)
                ->format('Y-m-d');
        });
    }

    /**
     * Formats the date keys of the grouped collection into human-readable strings.
     *
     * @param Collection $groupedLogs
     * @param string $userTimezone
     * @return Collection
     */
    private function formatDateKeys(Collection $groupedLogs, string $userTimezone): Collection
    {
        $nowInUserTz = Carbon::now($userTimezone);

        return $groupedLogs->mapWithKeys(function ($logs, $date) use ($userTimezone, $nowInUserTz) {
            $carbonDate = Carbon::parse($date, $userTimezone);
            $formattedDateKey = $this->getFormattedDateKey($carbonDate, $nowInUserTz);

            return [$formattedDateKey => $logs->toArray()];
        });
    }

    /**
     * Gets a formatted date key based on the relationship to today.
     *
     * @param Carbon $date
     * @param Carbon $now
     * @return string
     */
    private function getFormattedDateKey(Carbon $date, Carbon $now): string
    {
        if ($date->isSameDay($now)) {
            return __('Today');
        } elseif ($date->isSameDay($now->copy()->subDay())) {
            return __('Yesterday');
        }

        return $date->isoFormat('dddd, D [de] MMMM [de] YYYY');
    }

    /**
     * Extracts pagination metadata from the resource collection.
     *
     * @param ResourceCollection $transformedLogs
     * @return array
     */
    private function extractPaginationData(ResourceCollection $transformedLogs): array
    {
        $responseData = $transformedLogs->response()->getData();

        return [
            'meta' => $responseData->meta ?? null,
            'links' => $responseData->links ?? null,
        ];
    }
}
