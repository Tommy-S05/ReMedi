<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\MedicationLogStatusEnum;
use App\Models\MedicationTakeLog;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Throwable;

final class MedicationLogService
{
    /**
     * Records a user's action on a medication reminder notification.
     *
     * @param User $user The user performing the action.
     * @param string $notificationId The UUID of the notification being acted upon.
     * @param MedicationLogStatusEnum $status The action taken by the user (e.g., TAKEN, SKIPPED).
     * @return MedicationTakeLog The created log entry.
     *
     * @throws Throwable If the notification is not found, doesn't belong to the user, or an error occurs.
     */
    public function recordActionFromNotification(User $user, string $notificationId, MedicationLogStatusEnum $status): MedicationTakeLog
    {
        /** @var DatabaseNotification|null $notification */
        $notification = $user->notifications()->find($notificationId);

        if (!$notification) {
            // Lanzar una excepción si la notificación no se encuentra o no pertenece al usuario.
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Notification not found or access denied.');
        }

        $data = $notification->data;

        // Verificar que la notificación tenga la data necesaria.
        if (empty($data['medication_id']) || empty($data['schedule_id']) || empty($data['reminder_time_iso'])) {
            throw new InvalidArgumentException('Notification data is incomplete.');
        }

        try {
            // Crear el registro en el historial.
            $medicationTakeLog = MedicationTakeLog::create([
                'user_id' => $user->id,
                'medication_id' => $data['medication_id'],
                'medication_schedule_id' => $data['schedule_id'],
                'status' => $status,
                'scheduled_for' => Carbon::parse($data['reminder_time_iso']), // La hora UTC programada
                'action_taken_at' => Carbon::now(), // La hora actual en que se realiza la acción
                'notes' => null, // Opcionalmente, se podría añadir un campo de notas en el futuro
            ]);

            // Marcar la notificación como leída.
            $notification->markAsRead();

            return $medicationTakeLog;
        } catch (Throwable $e) {
            Log::error(__METHOD__ . ' failed: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => $user->id,
                'notification_id' => $notificationId,
                'status' => $status->value,
            ]);
            throw $e;
        }
    }
}
