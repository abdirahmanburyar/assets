<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the roles that have this permission.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Get the users that have this permission through their roles.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user', 'permission_id', 'user_id')
            ->using(Role::class);
    }

    /**
     * Check if the permission is assigned to a specific role.
     */
    public function isAssignedToRole(Role $role): bool
    {
        return $this->roles()->where('id', $role->id)->exists();
    }

    /**
     * Check if the permission is assigned to any of the given roles.
     */
    public function isAssignedToAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('id', $roles)->exists();
    }
}
