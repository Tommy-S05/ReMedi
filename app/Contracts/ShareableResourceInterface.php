<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\User;

interface ShareableResourceInterface
{
    /**
     * Determine if the user can share this resource.
     */
    public function canBeSharedBy(User $user): bool;

    /**
     * Get the display name of the resource type.
     */
    public function getShareableTypeName(): string;

    /**
     * Get additional data for the notification.
     */
    public function getShareableData(): array;

    /**
     * Obtiene el ID del recurso
     */
    // public function getShareableId(): int;
}
