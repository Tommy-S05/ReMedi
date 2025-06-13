<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\MedicationLogStatusEnum;
use App\Http\Requests\LogMedicationTakeRequest;
use App\Models\User;
use App\Services\MedicationLogService;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use Throwable;

final class MedicationLogController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected readonly MedicationLogService $medicationLogService
    ) {}

    /**
     * Store a new medication take log entry based on a user action.
     *
     * @param LogMedicationTakeRequest $request
     * @return JsonResponse
     */
    public function store(LogMedicationTakeRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $status = MedicationLogStatusEnum::from($request->validated('status'));
        $notificationId = $request->validated('notification_id');

        try {
            $medicationTakeLog = $this->medicationLogService->recordActionFromNotification(
                $user,
                $notificationId,
                $status
            );

            return $this->successResponse(
                data: $medicationTakeLog,
                message: __('messages.medication_log_recorded_successfully'),
            );
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse(
                message: __('messages.notification_not_found')
            );
        } catch (InvalidArgumentException $e) {
            return $this->validationErrorResponse(
                errors: ['notification' => $e->getMessage()],
                message: __('messages.failed_to_record_medication_log'),
            );
        } catch (Throwable $e) {
            return $this->serverErrorResponse(
                message: __('messages.failed_to_record_medication_log'),
            );
        }
    }
}
