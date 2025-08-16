<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Region extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    // Relationships
    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function assets(): HasManyThrough
    {
        return $this->hasManyThrough(Asset::class, Location::class);
    }

    // Accessors
    public function getActiveLocationsCountAttribute(): int
    {
        return $this->locations()->where('is_active', true)->count();
    }

    public function getTotalAssetsCountAttribute(): int
    {
        return $this->assets()->count();
    }

    public function getActiveAssetsCountAttribute(): int
    {
        return $this->assets()->where('status', 'in_use')->count();
    }

    // Scopes
    // Note: No active scope since we removed the is_active field
}
