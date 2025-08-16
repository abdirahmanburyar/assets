<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FundedSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return "{$this->name} ({$this->code})";
    }

    public function getActiveAssetsCountAttribute(): int
    {
        return $this->assets()->where('status', 'in_use')->count();
    }

    public function getTotalAssetsValueAttribute(): float
    {
        return $this->assets()->sum('current_value');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCode($query, string $code)
    {
        return $query->where('code', $code);
    }
}
