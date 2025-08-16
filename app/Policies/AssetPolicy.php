<?php

namespace App\Policies;

use App\Models\Asset;
use App\Models\User;

class AssetPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('asset.view') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Asset $asset): bool
    {
        return $user->hasPermission('asset.view') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('asset.create') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Asset $asset): bool
    {
        return $user->hasPermission('asset.edit') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Asset $asset): bool
    {
        return $user->hasPermission('asset.delete') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can approve the asset.
     */
    public function approve(User $user, Asset $asset): bool
    {
        return $user->hasPermission('asset.approve') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can reject the asset.
     */
    public function reject(User $user, Asset $asset): bool
    {
        return $user->hasPermission('asset.reject') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can transfer the asset.
     */
    public function transfer(User $user, Asset $asset): bool
    {
        return $user->hasPermission('asset.transfer') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can retire the asset.
     */
    public function retire(User $user, Asset $asset): bool
    {
        return $user->hasPermission('asset.retire') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Asset $asset): bool
    {
        return $user->hasPermission('asset.edit') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Asset $asset): bool
    {
        return $user->hasPermission('asset.delete') || $user->isSuperAdmin();
    }
}
