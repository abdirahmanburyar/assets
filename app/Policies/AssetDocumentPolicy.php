<?php

namespace App\Policies;

use App\Models\AssetDocument;
use App\Models\User;

class AssetDocumentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('asset_document.view') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AssetDocument $assetDocument): bool
    {
        return $user->hasPermission('asset_document.view') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('asset_document.upload') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AssetDocument $assetDocument): bool
    {
        return $user->hasPermission('asset_document.edit') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AssetDocument $assetDocument): bool
    {
        return $user->hasPermission('asset_document.delete') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can download the document.
     */
    public function download(User $user, AssetDocument $assetDocument): bool
    {
        return $user->hasPermission('asset_document.download') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AssetDocument $assetDocument): bool
    {
        return $user->hasPermission('asset_document.edit') || $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AssetDocument $assetDocument): bool
    {
        return $user->hasPermission('asset_document.delete') || $user->isSuperAdmin();
    }
}
