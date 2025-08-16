<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'region_id',
        'name',
    ];

    // Relationships
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function subLocations(): HasMany
    {
        return $this->hasMany(SubLocation::class);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return $this->region ? "{$this->region->name} - {$this->name}" : $this->name;
    }

    public function getActiveSubLocationsCountAttribute(): int
    {
        return $this->subLocations()->where('is_active', true)->count();
    }

    public function getActiveAssetsCountAttribute(): int
    {
        return $this->subLocations()
            ->whereHas('assets', function ($query) {
                $query->where('status', 'in_use');
            })
            ->count();
    }

    // Scopes
    public function scopeByRegion($query, $regionId)
    {
        return $query->where('region_id', $regionId);
    }
}
