<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'name',
    ];

    // Relationships
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return $this->location ? "{$this->location->name} - {$this->name}" : $this->name;
    }

    public function getLocationPathAttribute(): string
    {
        if (!$this->location) return $this->name;
        
        $regionName = $this->location->region ? $this->location->region->name : 'Unknown Region';
        return "{$regionName} > {$this->location->name} > {$this->name}";
    }

    public function getActiveAssetsCountAttribute(): int
    {
        return $this->assets()->where('status', 'in_use')->count();
    }

    // Scopes
    public function scopeByLocation($query, $locationId)
    {
        return $query->where('location_id', $locationId);
    }
}
