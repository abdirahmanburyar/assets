<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_tag',
        'category_id',
        'serial_number',
        'item_description',
        'assigned_to_id',
        'location_id',
        'sub_location_id',
        'acquisition_date',
        'status',
        'original_value',
        'current_value',
        'depreciation_rate',
        'depreciation_start_date',
        'warranty_expiry',
        'maintenance_interval_days',
        'next_maintenance_date',
        'funded_source_id',
        'created_by',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'depreciation_start_date' => 'date',
        'warranty_expiry' => 'date',
        'next_maintenance_date' => 'date',
        'original_value' => 'decimal:2',
        'current_value' => 'decimal:2',
        'depreciation_rate' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    protected $appends = [
        'depreciation_age',
        'is_warranty_expiring_soon',
        'is_maintenance_due_soon',
        'status_color',
        'full_location_path',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($asset) {
            if (auth()->check()) {
                $asset->created_by = auth()->id();
            }
            
            // Calculate current value based on original value and depreciation
            if ($asset->original_value && $asset->depreciation_rate && $asset->depreciation_start_date) {
                $asset->current_value = $asset->calculateCurrentValue();
            } else {
                $asset->current_value = $asset->original_value;
            }
        });

        static::updating(function ($asset) {
            // Recalculate current value when updating
            if ($asset->isDirty(['original_value', 'depreciation_rate', 'depreciation_start_date'])) {
                $asset->current_value = $asset->calculateCurrentValue();
            }
        });
    }

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function subLocation(): BelongsTo
    {
        return $this->belongsTo(SubLocation::class);
    }

    // Access region through location
    public function getRegionAttribute()
    {
        return $this->location?->region;
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(Assignee::class, 'assigned_to_id');
    }

    public function fundedSource(): BelongsTo
    {
        return $this->belongsTo(FundedSource::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(AssetDocument::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(AssetHistory::class);
    }

    public function latestHistory(): HasOne
    {
        return $this->hasOne(AssetHistory::class)->latestOfMany();
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInUse($query)
    {
        return $query->where('status', 'in_use');
    }

    public function scopeMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }

    public function scopeRetired($query)
    {
        return $query->where('status', 'retired');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeWarrantyExpiringSoon($query, $days = 30)
    {
        return $query->where('warranty_expiry', '<=', now()->addDays($days))
                    ->where('warranty_expiry', '>', now());
    }

    public function scopeMaintenanceDueSoon($query, $days = 7)
    {
        return $query->where('next_maintenance_date', '<=', now()->addDays($days))
                    ->where('next_maintenance_date', '>', now())
                    ->where('status', 'in_use');
    }

    public function scopeByRegion($query, int $regionId)
    {
        return $query->whereHas('location', function ($q) use ($regionId) {
            $q->where('region_id', $regionId);
        });
    }

    public function scopeByLocation($query, int $locationId)
    {
        return $query->where('location_id', $locationId);
    }

    public function scopeBySubLocation($query, int $subLocationId)
    {
        return $query->where('sub_location_id', $subLocationId);
    }

    // Scope to load all relationships for view page
    public function scopeWithViewRelations($query)
    {
        return $query->with([
            'category',
            'location.region',
            'subLocation',
            'assignee',
            'fundedSource',
            'creator',
            'approver',
            'documents.uploader',
        ]);
    }

    // Accessors
    public function getDepreciationAgeAttribute(): int
    {
        if (!$this->depreciation_start_date) {
            return 0;
        }

        return $this->depreciation_start_date->diffInYears(now());
    }

    public function getIsWarrantyExpiringSoonAttribute(): bool
    {
        if (!$this->warranty_expiry) {
            return false;
        }

        return $this->warranty_expiry->diffInDays(now()) <= 30;
    }

    public function getIsMaintenanceDueSoonAttribute(): bool
    {
        if (!$this->next_maintenance_date) {
            return false;
        }

        return $this->next_maintenance_date->diffInDays(now()) <= 7;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'in_use' => 'success',
            'maintenance' => 'info',
            'retired' => 'gray',
            'rejected' => 'danger',
            default => 'gray',
        };
    }

    public function getFullLocationPathAttribute(): string
    {
        $path = [$this->location->region->name, $this->location->name];
        
        if ($this->subLocation) {
            $path[] = $this->subLocation->name;
        }
        
        return implode(' > ', $path);
    }

    // Methods
    public function calculateCurrentValue(): float
    {
        if ($this->depreciation_rate <= 0) {
            return $this->original_value;
        }

        $age = $this->depreciation_age;
        $depreciationAmount = $this->original_value * ($this->depreciation_rate / 100) * $age;
        
        return max(0, $this->original_value - $depreciationAmount);
    }

    public function updateCurrentValue(): void
    {
        $this->current_value = $this->calculateCurrentValue();
        $this->save();
    }

    public function calculateNextMaintenanceDate(): ?Carbon
    {
        if ($this->maintenance_interval_days <= 0) {
            return null;
        }

        $lastMaintenance = $this->histories()
            ->where('action_type', 'maintenance')
            ->latest()
            ->first();

        if ($lastMaintenance) {
            return $lastMaintenance->created_at->addDays($this->maintenance_interval_days);
        }

        return $this->acquisition_date->addDays($this->maintenance_interval_days);
    }

    public function updateNextMaintenanceDate(): void
    {
        $this->next_maintenance_date = $this->calculateNextMaintenanceDate();
        $this->save();
    }

    public function approve(User $user): void
    {
        $this->update([
            'status' => 'in_use',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        $this->recordHistory('status_change', 'status', 'pending', 'in_use', 'Asset approved');
    }

    public function reject(User $user, string $reason = null): void
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        $this->recordHistory('status_change', 'status', 'pending', 'rejected', 'Asset rejected: ' . $reason);
    }

    public function recordHistory(string $actionType, ?string $fieldName = null, $oldValue = null, $newValue = null, string $description = null): void
    {
        $this->histories()->create([
            'action_type' => $actionType,
            'field_name' => $fieldName,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'description' => $description ?? "Field {$fieldName} changed from {$oldValue} to {$newValue}",
            'changed_by' => auth()->id(),
        ]);
    }

    public function transferToLocation(Location $newLocation, ?SubLocation $newSubLocation = null, User $user = null): void
    {
        $oldLocation = $this->location;
        $oldSubLocation = $this->subLocation;
        $oldLocationPath = $oldSubLocation ? "{$oldLocation->name} > {$oldSubLocation->name}" : $oldLocation->name;
        $newLocationPath = $newSubLocation ? "{$newLocation->name} > {$newSubLocation->name}" : $newLocation->name;

        $this->update([
            'location_id' => $newLocation->id,
            'sub_location_id' => $newSubLocation?->id,
        ]);

        $this->recordHistory(
            'location_transfer',
            'location',
            $oldLocationPath,
            $newLocationPath,
            "Asset transferred to {$newLocationPath}"
        );
    }

    public function assignTo(Assignee $assignee, User $user = null): void
    {
        $oldAssignee = $this->assignee;
        $oldAssigneeName = $oldAssignee ? $oldAssignee->name : 'Unassigned';

        $this->update(['assigned_to_id' => $assignee->id]);

        $this->recordHistory(
            'assignment',
            'assigned_to_id',
            $oldAssigneeName,
            $assignee->name,
            "Asset assigned to {$assignee->name}"
        );
    }

    public function markForMaintenance(string $reason = null, User $user = null): void
    {
        $this->update(['status' => 'maintenance']);

        $this->recordHistory(
            'status_change',
            'status',
            'in_use',
            'maintenance',
            "Asset marked for maintenance: {$reason}"
        );
    }

    public function completeMaintenance(string $notes = null, User $user = null): void
    {
        $this->update([
            'status' => 'in_use',
            'next_maintenance_date' => $this->calculateNextMaintenanceDate(),
        ]);

        $this->recordHistory(
            'maintenance',
            'status',
            'maintenance',
            'in_use',
            "Maintenance completed: {$notes}"
        );
    }

    public function retire(string $reason = null, User $user = null): void
    {
        $this->update(['status' => 'retired']);

        $this->recordHistory(
            'status_change',
            'status',
            $this->getOriginal('status'),
            'retired',
            "Asset retired: {$reason}"
        );
    }
}
