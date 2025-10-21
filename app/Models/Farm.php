<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Farm extends Model
{
    use LogsActivity;
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'description',
                'is_active',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['role', 'is_visible','role'])
            ->withTimestamps()
            ->wherePivot('is_visible', true);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'farm_category_visibility')
            ->withPivot('is_visible')
            ->withTimestamps();
    }

    public function inventoryItems(): BelongsToMany
    {
        return $this->belongsToMany(InventoryItem::class, 'farm_inventory_visibility')
            ->withPivot('is_visible')
            ->withTimestamps();
    }
}
