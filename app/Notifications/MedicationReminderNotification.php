<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Medication;
use App\Models\MedicationSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

final class MedicationReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param Medication         $medication   The medication to be reminded about.
     * @param MedicationSchedule $schedule     The specific schedule triggering this reminder.
     * @param Carbon             $reminderTime The exact time instance this reminder is for.
     */
    public function __construct(
        public Medication $medication,
        public MedicationSchedule $schedule,
        public Carbon $reminderTime // La hora exacta para la que es este recordatorio
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed              $notifiable The notifiable entity (User).
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     * (Opcional por ahora, pero requerido si 'mail' está en via())
     *
     * @param  mixed       $notifiable
     * @return MailMessage
     */
    // public function toMail(mixed $notifiable): MailMessage
    // {
    //     return (new MailMessage)
    //                 ->subject(__('notifications.medication_reminder_subject', ['medication' => $this->medication->name]))
    //                 ->line(__('notifications.medication_reminder_greeting', ['name' => $notifiable->name]))
    //                 ->line(__('notifications.medication_reminder_line1', [
    //                     'time' => $this->reminderTime->format('h:i A'), // Formatear la hora
    //                     'medication' => $this->medication->name,
    //                     'dosage' => $this->medication->dosage ?? ''
    //                 ]))
    //                 ->action(__('notifications.medication_reminder_action'), url('/medications')) // O a una página específica
    //                 ->line(__('notifications.medication_reminder_thank_you'));
    // }

    /**
     * Get the array representation of the notification.
     * Esto es lo que se almacenará en la columna 'data' de la tabla 'notifications'.
     *
     * @param  mixed                $notifiable
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'medication_id' => $this->medication->id,
            'medication_name' => $this->medication->name,
            'medication_type' => $this->medication->type?->label() ?? $this->medication->type?->value,
            'dosage' => $this->medication->dosage,
            'schedule_id' => $this->schedule->id,
            'reminder_time_iso' => $this->reminderTime->toIso8601String(), // Hora exacta del recordatorio en ISO8601
            'time_to_take' => $this->schedule->time_to_take, // HH:mm de la configuración del schedule
            'instructions' => $this->medication->instructions,
            // Podrías añadir un mensaje pre-formateado y traducido aquí también
            // TODO : añadir key al archivo de traducciones
            'message_key' => 'Take your medication reminder', // Clave para traducción en el frontend
            'message_params' => [ // Parámetros para la traducción
                'time' => $this->reminderTime->copy()->setTimezone($notifiable->timezone ?? config('app.timezone'))->format('h:i A'), // Hora en la zona horaria del usuario
                'medication' => $this->medication->name,
                'dosage' => $this->medication->dosage ?? '',
            ],
        ];
    }

    /**
     * Get the data to broadcast for the notification. (Opcional, para WebSockets)
     *
     * @param  mixed                $notifiable
     * @return array<string, mixed>
     */
    // public function toBroadcast(mixed $notifiable): array
    // {
    //     return [
    //         'data' => $this->toArray($notifiable), // Reutilizar toArray
    //         'read_at' => null, // Para la UI en tiempo real
    //         'created_at' => now()->toIso8601String(), // Para la UI en tiempo real
    //     ];
    // }
}
