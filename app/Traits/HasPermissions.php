<?php

namespace App\Traits;

use App\Models\User;

trait HasPermissions
{
    /**
     * Check if the current user has a specific permission.
     */
    public static function userCan(string $permission): bool
    {
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }
        
        return $user->hasPermission($permission) || $user->isSuperAdmin();
    }

    /**
     * Check if the current user has any of the given permissions.
     */
    public static function userCanAny(array $permissions): bool
    {
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }
        
        return $user->hasAnyPermission($permissions) || $user->isSuperAdmin();
    }

    /**
     * Check if the current user has a specific role.
     */
    public static function userHasRole(string $role): bool
    {
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }
        
        return $user->hasRole($role) || $user->isSuperAdmin();
    }

    /**
     * Check if the current user has any of the given roles.
     */
    public static function userHasAnyRole(array $roles): bool
    {
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }
        
        return $user->hasAnyRole($roles) || $user->isSuperAdmin();
    }

    /**
     * Check if the current user is a super admin.
     */
    public static function userIsSuperAdmin(): bool
    {
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }
        
        return $user->isSuperAdmin();
    }
}
