<?php

namespace App\Policies;

use App\Models\Prescription;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class PrescriptionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return Auth::check();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Prescription $prescription
     * @return bool
     */
    public function view(User $user, Prescription $prescription): bool
    {
        return $user->id === $prescription->user_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return Auth::check();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Prescription $prescription
     * @return bool
     */
    public function update(User $user, Prescription $prescription): bool
    {
        return $user->id === $prescription->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Prescription $prescription
     * @return bool
     */
    public function delete(User $user, Prescription $prescription): bool
    {
        return $user->id === $prescription->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Prescription $prescription): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Prescription $prescription): bool
    {
        return false;
    }
}
