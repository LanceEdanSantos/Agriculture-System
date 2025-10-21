<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ItemRequestAttachment extends Model
{
    protected $fillable = [
        'item_request_id',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'original_name',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    /**
     * Get the item request that owns the attachment.
     */
    public function itemRequest(): BelongsTo
    {
        return $this->belongsTo(ItemRequest::class);
    }

    /**
     * Get the full URL for the attachment file.
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    /**
     * Get the file size in human readable format.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if the file is an image.
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Delete the attachment and its file.
     */
    public function deleteWithFile(): bool
    {
        // Delete the physical file
        if (Storage::exists($this->file_path)) {
            Storage::delete($this->file_path);
        }

        // Delete the database record
        return $this->delete();
    }

    /**
     * Get the file extension.
     */
    public function getFileExtensionAttribute(): string
    {
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }
}
