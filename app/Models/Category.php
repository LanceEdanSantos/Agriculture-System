<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'icon',
        'is_active',
        'sort_order',
    ];


    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the inventory items in this category
     */
    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class);
    }

    /**
     * Get the purchase request items in this category
     */
    public function purchaseRequestItems()
    {
        return $this->hasMany(PurchaseRequestItem::class);
    }

    /**
     * Check if category is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Get formatted color
     */
    public function getFormattedColorAttribute(): string
    {
        return $this->color ?? '#3B82F6';
    }

    /**
     * Get display name with icon
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->icon) {
            return "{$this->icon} {$this->name}";
        }
        return $this->name;
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered categories
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Boot method to auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }
    
    /**
     * Get farms associated with this category
     */
    public function farms()
    {
        return $this->belongsToMany(Farm::class, 'farm_category_visibility')
            ->withPivot('is_visible')
            ->withTimestamps();
    }

}
