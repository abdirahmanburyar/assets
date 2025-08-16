<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class AssetDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'file_name',
        'file_path',
        'uploaded_by',
    ];

    // Relationships
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Accessors
    public function getFileSizeFormattedAttribute(): string
    {
        try {
            if (!Storage::exists($this->file_path)) {
                return 'File not found';
            }

            $bytes = Storage::size($this->file_path);
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];

            for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
                $bytes /= 1024;
            }

            return round($bytes, 2) . ' ' . $units[$i];
        } catch (\Exception $e) {
            \Log::warning('Could not get file size for AssetDocument', [
                'id' => $this->id,
                'file_path' => $this->file_path,
                'error' => $e->getMessage()
            ]);
            return 'Size unknown';
        }
    }

    public function getDownloadUrlAttribute(): string
    {
        try {
            if (!Storage::exists($this->file_path)) {
                \Log::warning('File not found for download', [
                    'id' => $this->id,
                    'file_path' => $this->file_path
                ]);
                return '#';
            }
            
            return Storage::url($this->file_path);
        } catch (\Exception $e) {
            \Log::warning('Could not generate download URL for AssetDocument', [
                'id' => $this->id,
                'file_path' => $this->file_path,
                'error' => $e->getMessage()
            ]);
            return '#';
        }
    }

    // Methods
    public function deleteFile(): bool
    {
        if (Storage::exists($this->file_path)) {
            return Storage::delete($this->file_path);
        }

        return true;
    }
}
