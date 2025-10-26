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
        'display_name',
        'abbreviation',
        'category_id',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'display_name',
                'abbreviation',
                'category_id',
                'description',
                'is_active',
                'sort_order',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the category this unit belongs to
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Check if unit is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
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
        return $query->where('is_active', true);
    }

    /**
     * Scope for standard units (not custom)
     */
    public function scopeStandard($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for custom units
     */
    public function scopeCustom($query)
    {
        return $query->where('is_active', false);
    }
}
