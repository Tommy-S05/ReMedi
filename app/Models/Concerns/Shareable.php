<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Enums\ShareStatusEnum;
use App\Models\ResourceShare;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Shareable
{
    /**
     * Get all the resource's shares.
     */
    public function shares(): MorphMany
    {
        return $this->morphMany(ResourceShare::class, 'shareable');
    }

    /**
     * Get active (accepted) shares for the resource.
     */
    public function activeShares(): MorphMany
    {
        return $this->shares()->where('status', ShareStatusEnum::ACCEPTED);
    }

    /**
     * Get pending shares for the resource.
     */
    public function pendingShares(): MorphMany
    {
        return $this->shares()->where('status', ShareStatusEnum::PENDING);
    }

    /**
     * Check if resource is shared with a specific user
     */
    public function isSharedWith(User $user): bool
    {
        return $this->activeShares()
            ->where('shared_with_user_id', $user->id)
            ->exists();
    }

    /**
     * Check if the resource has a pending or accepted share with a specific email.
     */
    public function isSharedWithEmail(string $email): bool
    {
        return $this->shares()
            ->where('shared_with_email', $email)
            ->whereIn('status', [ShareStatusEnum::PENDING, ShareStatusEnum::ACCEPTED])
            ->exists();
    }

    /**
     * Get all users with whom this resource has been actively shared.
     */
    public function sharedWithUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'resource_shares',
            'shareable_id',
            'shared_with_user_id'
        )->wherePivot('shareable_type', get_class($this))
            ->wherePivot('status', ShareStatusEnum::ACCEPTED);
    }
}
