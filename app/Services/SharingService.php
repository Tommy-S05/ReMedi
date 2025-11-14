<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ShareStatusEnum;
use App\Models\ResourceShare;
use App\Models\User;
use App\Notifications\ResourceSharedNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Throwable;

final class SharingService
{
    /**
     * Create and send a sharing invitation.
     *
     * @param User $owner The user who is sharing the resource.
     * @param Model $shareable The resource being shared (Medication or Prescription).
     * @param string $recipientEmail The email of the person to share with.
     * @return ResourceShare
     *
     * @throws Exception|Throwable
     */
    public function createInvitation(User $owner, Model $shareable, string $recipientEmail): ResourceShare
    {
        return DB::transaction(function () use ($owner, $shareable, $recipientEmail) {
            $this->validateInvitation($owner, $shareable, $recipientEmail);

            $share = $shareable->shares()->create([
                'owner_user_id' => $owner->id,
                'shared_with_email' => $recipientEmail,
                'status' => ShareStatusEnum::PENDING,
                'token' => Str::random(40),
                'expires_at' => Carbon::now()->addDays(7), // Expiración opcional
            ]);

            // Si el usuario ya existe, asociarlo
            $existingUser = User::where('email', $recipientEmail)->first();
            if ($existingUser) {
                $share->update(['shared_with_user_id' => $existingUser->id]);
            }

            Notification::route('mail', $recipientEmail)
                ->notify(new ResourceSharedNotification($share));

            return $share;
        });
    }

    /**
     * Accept a pending share invitation.
     *
     * @param string $token The unique invitation token.
     * @param User $acceptingUser The user who is accepting the invitation.
     * @return ResourceShare
     *
     * @throws InvalidArgumentException|Throwable
     */
    public function acceptInvitation(string $token, User $acceptingUser): ResourceShare
    {
        return DB::transaction(function () use ($token, $acceptingUser) {
            try {
                $share = ResourceShare::where('token', $token)
                    ->firstOrFail();
            } catch (ModelNotFoundException $e) {
                throw new InvalidArgumentException('This invitation is invalid.');
            }

            if (Str::lower($share->shared_with_email) !== Str::lower($acceptingUser->email)) {
                throw new InvalidArgumentException('This invitation is intended for another user.');
            }

            if ($share?->expires_at?->isPast()) {
                $share->update(['status' => ShareStatusEnum::EXPIRED]);
                throw new InvalidArgumentException('This invitation has expired.');
            }

            if ($share->status !== ShareStatusEnum::PENDING) {
                throw new InvalidArgumentException('This invitation has already been actioned.');
            }

            $share->update([
                'shared_with_user_id' => $acceptingUser->id,
                'status' => ShareStatusEnum::ACCEPTED,
            ]);

            // $share->owner->notify(new ShareAcceptedNotification($share));

            return $share;
        });
    }

    /**
     * Revoke a shared resource.
     *
     * @param ResourceShare $share
     * @param User $user
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function revokeShare(ResourceShare $share, User $user): void
    {
        // Solo el propietario puede revocar
        if ($share->owner_user_id !== $user->id) {
            throw new InvalidArgumentException('You can only revoke your own shares.');
        }

        $share->update(['status' => ShareStatusEnum::REVOKED]);
    }

    /**
     * Get all shared resources for a user.
     *
     * @param User $user
     * @return Collection<int|string, Collection<int, ResourceShare>>
     */
    public function getSharedResources(User $user): Collection
    {
        return ResourceShare::where('shared_with_user_id', $user->id)
            ->where('status', ShareStatusEnum::ACCEPTED)
            ->with('shareable')
            ->get()
            ->groupBy('shareable_type');
    }

    /**
     * Validate the sharing invitation.
     *
     * @param User $owner
     * @param Model $shareable
     * @param string $recipientEmail
     * @return void
     *
     * @throws InvalidArgumentException
     */
    private function validateInvitation(User $owner, Model $shareable, string $recipientEmail): void
    {
        if ($owner->email === $recipientEmail) {
            throw new InvalidArgumentException('You cannot share a resource with yourself.');
        }

        if ($shareable->isSharedWithEmail($recipientEmail)) {
            throw new InvalidArgumentException('This resource has already been shared with this user.');
        }
    }
}
