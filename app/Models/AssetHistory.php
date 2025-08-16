<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'action_type',
        'field_name',
        'old_value',
        'new_value',
        'description',
        'changed_by',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    // Relationships
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // Accessors
    public function getActionTypeLabelAttribute(): string
    {
        return match($this->action_type) {
            'status_change' => 'Status Change',
            'location_transfer' => 'Location Transfer',
            'assignment' => 'Assignment',
            'maintenance' => 'Maintenance',
            'depreciation_update' => 'Depreciation Update',
            'document_upload' => 'Document Upload',
            'document_delete' => 'Document Deleted',
            default => ucfirst(str_replace('_', ' ', $this->action_type)),
        };
    }

    public function getActionTypeColorAttribute(): string
    {
        return match($this->action_type) {
            'status_change' => 'primary',
            'location_transfer' => 'info',
            'assignment' => 'success',
            'maintenance' => 'warning',
            'depreciation_update' => 'secondary',
            'document_upload' => 'success',
            'document_delete' => 'danger',
            default => 'gray',
        };
    }

    public function getActionTypeIconAttribute(): string
    {
        return match($this->action_type) {
            'status_change' => 'heroicon-o-arrow-path',
            'location_transfer' => 'heroicon-o-truck',
            'assignment' => 'heroicon-o-user-plus',
            'maintenance' => 'heroicon-o-wrench-screwdriver',
            'depreciation_update' => 'heroicon-o-calculator',
            'document_upload' => 'heroicon-o-document-plus',
            'document_delete' => 'heroicon-o-document-minus',
            default => 'heroicon-o-information-circle',
        };
    }

    public function getFormattedOldValueAttribute(): string
    {
        if ($this->field_name === 'status') {
            return ucfirst(str_replace('_', ' ', $this->old_value ?? 'N/A'));
        }

        if ($this->field_name === 'location_id' && $this->old_value) {
            $location = Location::find($this->old_value);
            return $location ? $location->name : $this->old_value;
        }

        if ($this->field_name === 'assigned_to_id' && $this->old_value) {
            $assignee = Assignee::find($this->old_value);
            return $assignee ? $assignee->name : $this->old_value;
        }

        return $this->old_value ?? 'N/A';
    }

    public function getFormattedNewValueAttribute(): string
    {
        if ($this->field_name === 'status') {
            return ucfirst(str_replace('_', ' ', $this->new_value ?? 'N/A'));
        }

        if ($this->field_name === 'location_id' && $this->new_value) {
            $location = Location::find($this->new_value);
            return $location ? $location->name : $this->new_value;
        }

        if ($this->field_name === 'assigned_to_id' && $this->new_value) {
            $assignee = Assignee::find($this->new_value);
            return $assignee ? $assignee->name : $this->new_value;
        }

        return $this->new_value ?? 'N/A';
    }

    // Scopes
    public function scopeByActionType($query, string $actionType)
    {
        return $query->where('action_type', $actionType);
    }

    public function scopeByField($query, string $fieldName)
    {
        return $query->where('field_name', $fieldName);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('changed_by', $userId);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Methods
    public function getMetadataValue(string $key, $default = null)
    {
        return data_get($this->metadata, $key, $default);
    }

    public function setMetadataValue(string $key, $value): void
    {
        $metadata = $this->metadata ?? [];
        $metadata[$key] = $value;
        $this->metadata = $metadata;
    }
}
