<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Unit extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'abbreviation',
        'category',
        'description',
        'is_custom',
        'status',
    ];

    protected $casts = [
        'is_custom' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'abbreviation',
                'category',
                'description',
                'is_custom',
                'status',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the inventory items using this unit
     */
    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class);
    }

    /**
     * Check if unit is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if unit is custom
     */
    public function isCustom(): bool
    {
        return $this->is_custom;
    }

    /**
     * Get display name
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->abbreviation) {
            return "{$this->name} ({$this->abbreviation})";
        }
        return $this->name;
    }

    /**
     * Scope for active units
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for standard units (not custom)
     */
    public function scopeStandard($query)
    {
        return $query->where('is_custom', false);
    }

    /**
     * Scope for custom units
     */
    public function scopeCustom($query)
    {
        return $query->where('is_custom', true);
    }
}
