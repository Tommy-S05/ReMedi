<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

final class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboardService) {}

    /**
     * Display the user's dashboard.
     *
     * @return InertiaResponse
     */
    public function index(): InertiaResponse
    {
        $dashboardData = $this->dashboardService->getDataForUser(Auth::user());

        return Inertia::render('Dashboard', $dashboardData);
    }
}
