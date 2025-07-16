<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CalendarService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * Handles API requests for calendar events.
 */
final class CalendarController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private readonly CalendarService $calendarService) {}

    /**
     * Fetch calendar events for a given date range.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getEvents(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after_or_equal:start'],
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        /** @var User $user */
        $user = Auth::user();
        $userTimezone = $user->timezone ?? config('app.timezone');

        $startRange = Carbon::parse($request->input('start'), $userTimezone);
        $endRange = Carbon::parse($request->input('end'), $userTimezone);

        $events = $this->calendarService->getEventsForRange($user, $startRange, $endRange);

        return response()->json($events);
    }
}
