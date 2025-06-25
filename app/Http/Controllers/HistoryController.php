<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\HistoryService;
use App\Traits\ApiResponseTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

final class HistoryController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly HistoryService $historyService
    ) {}

    /**
     * Display the user's medication take history, grouped by day.
     *
     * @param Request $request
     * @return InertiaResponse|JsonResponse
     */
    public function index(Request $request): InertiaResponse|JsonResponse
    {
        $user = Auth::user();

        $perPage = $this->historyService->getPerPageFromRequest($request);

        if ($request->wantsJson()) {
            $page = $this->historyService->getPageFromRequest($request);
            $historyData = $this->historyService->getPaginatedGroupedHistory($user, $perPage, $page);
        } else {
            $historyData = $this->historyService->getPaginatedGroupedHistory($user, $perPage);
        }

        return $request->wantsJson()
            ? $this->successResponse($historyData)
            : Inertia::render('history/Index', $historyData);
    }

    /**
     * Fetch subsequent pages of medication history via API.
     *
     * @param Request $request
     * @return JsonResponse
     *
     * @throws AuthorizationException
     */
    public function fetch(Request $request): JsonResponse
    {
        $user = Auth::user();

        $perPage = $this->historyService->getPerPageFromRequest($request);
        $page = $this->historyService->getPageFromRequest($request);

        // Obtener los datos paginados y agrupados desde el servicio
        $historyData = $this->historyService->getPaginatedGroupedHistory($user, $perPage, $page);

        return $this->successResponse($historyData);
    }
}
