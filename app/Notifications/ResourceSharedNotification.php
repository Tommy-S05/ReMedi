<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\ResourceShare;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

final class ResourceSharedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public ResourceShare $share;

    /**
     * Create a new notification instance.
     */
    public function __construct(ResourceShare $share)
    {
        $this->share = $share;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Generar una URL firmada y temporal para aceptar la invitación.
        // Esto añade una capa extra de seguridad.
        $acceptUrl = URL::temporarySignedRoute(
            'shares.accept',
            now()->addDays(7), // El enlace de invitación expira en 7 días
            ['token' => $this->share->token]
        );

        $ownerName = $this->share->owner->name;
        $resourceType = class_basename($this->share->shareable_type);
        $resourceName = $this->share->shareable->name ?? 'un tratamiento';

        return (new MailMessage)
            ->subject(__('notifications.resource_shared.email.subject', ['ownerName' => $ownerName]))
            ->greeting(__('notifications.resource_shared.email.greeting'))
            ->line(__('notifications.resource_shared.email.line_1', ['ownerName' => $ownerName, 'resourceType' => $resourceType, 'resourceName' => $resourceName]))
            ->line(__('notifications.resource_shared.email.line_2'))
            ->action(__('notifications.resource_shared.email.action'), $acceptUrl)
            ->line(__('notifications.resource_shared.email.line_3'));
    }
}
