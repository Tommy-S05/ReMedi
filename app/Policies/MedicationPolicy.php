<?php

namespace App\Policies;

use App\Models\Medication;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MedicationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Medication $medication): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Medication $medication
     * @return bool
     */
    public function update(User $user, Medication $medication): bool
    {
        return $user->id === $medication->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     * @param User $user
     * @param Medication $medication
     * @return bool
     */
    public function delete(User $user, Medication $medication): bool
    {
        return $user->id === $medication->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Medication $medication): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Medication $medication): bool
    {
        return false;
    }
}
