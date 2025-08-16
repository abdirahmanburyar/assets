<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'department',
        'employee_id',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'assigned_to_id');
    }

    public function getActiveAssetsCountAttribute(): int
    {
        return $this->assets()->where('status', 'in_use')->count();
    }

    public function getTotalAssetsValueAttribute(): float
    {
        return $this->assets()->sum('current_value');
    }

    public function getMaintenanceDueAssetsAttribute()
    {
        return $this->assets()
            ->where('next_maintenance_date', '<=', now()->addDays(7))
            ->where('status', 'in_use')
            ->get();
    }
}
