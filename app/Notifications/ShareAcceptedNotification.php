<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\ResourceShare;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class ShareAcceptedNotification extends Notification implements ShouldQueue
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
     *
     * @param  mixed              $notifiable The notifiable entity (User).
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $recipientName = $this->share->sharedWithUser->name;
        $resourceType = class_basename($this->share->shareable_type);
        $resourceName = $this->share->shareable->name ?? $this->share->shareable->title ?? 'your resource';

        $resourceUrl = route(mb_strtolower($resourceType) . 's.show', $this->share->shareable_id);

        return (new MailMessage)
            ->subject(__('notifications.share_accepted.email.subject', ['recipientName' => $recipientName]))
            ->greeting(__('notifications.share_accepted.email.greeting', ['name' => $notifiable->name]))
            ->line(__('notifications.share_accepted.email.line_1', [
                'recipientName' => $recipientName,
                'resourceType' => __($resourceType),
                'resourceName' => $resourceName,
            ]))
            ->line(__('notifications.share_accepted.email.line_2'))
            ->action(__('notifications.share_accepted.email.action'), $resourceUrl)
            ->line(__('notifications.share_accepted.email.line_3'));
    }

    /**
     * Get the array representation of the notification.
     * This is what will be stored in the 'data' column of the 'notifications' table.
     *
     * @param  mixed                $notifiable
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'share_id' => $this->share->id,
            'recipient_user_id' => $this->share->shared_with_user_id,
            'recipient_name' => $this->share->sharedWithUser->name,
            'recipient_email' => $this->share->shared_with_email,
            'shareable_type' => $this->share->shareable_type,
            'shareable_id' => $this->share->shareable_id,
            'resource_type' => class_basename($this->share->shareable_type),
            'resource_name' => $this->share->shareable->name ?? $this->share->shareable->title ?? 'Resource',
            'message_key' => __('notifications.share_accepted.email.subject', ['recipientName' => $this->share->sharedWithUser->name]),
            'message_params' => [
                'recipientName' => $this->share->sharedWithUser->name,
                'resourceType' => class_basename($this->share->shareable_type),
                'resourceName' => $this->share->shareable->name ?? $this->share->shareable->title ?? 'Resource',
            ],
        ];
    }
}
