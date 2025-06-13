<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

final class NotificationController extends Controller
{
    use ApiResponseTrait;

    /**
     * Fetch the user's unread notifications.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        // Obtener notificaciones no leídas, con un límite para no sobrecargar
        $notifications = $user->unreadNotifications()->limit(15)->get();

        return $this->successResponse([
            'notifications' => $notifications,
            'total_unread' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark a specific notification as read.
     *
     * @param DatabaseNotification $notification
     * @return JsonResponse
     *
     * @throws AuthorizationException
     */
    public function markAsRead(DatabaseNotification $notification): JsonResponse
    {
        // La autorización implícita ocurre aquí: Laravel no encontrará la notificación
        // si intentas acceder a una que no es tuya a través del route model binding
        // si la relación se define correctamente. Para ser explícitos:
        if (Auth::id() !== $notification->notifiable_id) {
            return $this->forbiddenResponse();
        }

        $notification->markAsRead();

        return $this->successResponse(null, __('messages.notification_marked_as_read'));
    }

    /**
     * Mark all the user's unread notifications as read.
     *
     * @return JsonResponse
     */
    public function markAllAsRead(): JsonResponse
    {
        Auth::user()->unreadNotifications->markAsRead();

        return $this->successResponse(null, __('messages.all_notifications_marked_as_read'));
    }
}
