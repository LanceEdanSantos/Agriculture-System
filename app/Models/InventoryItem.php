<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'category_id',
        'unit',
        'unit_cost',
        'current_stock',
        'minimum_stock',
        'item_code',
        'supplier_id',
        'unit_id',
        'average_unit_cost',
        'total_purchased',
        'last_purchase_date',
        'last_supplier',
        'expiration_date',
        'notes',
        'status',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'average_unit_cost' => 'decimal:2',
        'expiration_date' => 'date',
        'last_purchase_date' => 'date',
    ];

    /**
     * Get the purchase request items for this inventory item
     */
    public function purchaseRequestItems()
    {
        return $this->hasMany(PurchaseRequestItem::class);
    }

    /**
     * Get the category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the supplier
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the unit
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the purchase history for this item
     */
    public function purchaseHistory()
    {
        return $this->hasMany(PurchaseHistory::class);
    }

    /**
     * Check if stock is low
     */
    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }

    /**
     * Check if item is expired
     */
    public function isExpired(): bool
    {
        return $this->expiration_date && $this->expiration_date->isPast();
    }

    /**
     * Get formatted unit cost
     */
    public function getFormattedUnitCostAttribute(): string
    {
        return '₱' . number_format($this->unit_cost, 2);
    }

    /**
     * Get formatted average unit cost
     */
    public function getFormattedAverageUnitCostAttribute(): string
    {
        return '₱' . number_format($this->average_unit_cost, 2);
    }

    /**
     * Get total value of current stock
     */
    public function getTotalStockValueAttribute(): float
    {
        return $this->current_stock * $this->unit_cost;
    }

    /**
     * Get total value using average cost
     */
    public function getTotalStockValueAverageAttribute(): float
    {
        return $this->current_stock * $this->average_unit_cost;
    }

    /**
     * Get the latest purchase history entry
     */
    public function getLatestPurchaseAttribute()
    {
        return $this->purchaseHistory()->latest('purchase_date')->first();
    }

    /**
     * Get the total purchase value
     */
    public function getTotalPurchaseValueAttribute(): float
    {
        return $this->purchaseHistory()->sum('total_cost');
    }

    /**
     * Update average unit cost based on purchase history
     */
    public function updateAverageUnitCost(): void
    {
        $purchases = $this->purchaseHistory()->where('status', '!=', 'cancelled')->get();

        if ($purchases->count() > 0) {
            $totalCost = $purchases->sum('total_cost');
            $totalQuantity = $purchases->sum('quantity_purchased');

            if ($totalQuantity > 0) {
                $this->average_unit_cost = $totalCost / $totalQuantity;
                $this->save();
            }
        }
    }

    /**
     * Update total purchased quantity
     */
    public function updateTotalPurchased(): void
    {
        $this->total_purchased = $this->purchaseHistory()
            ->where('status', '!=', 'cancelled')
            ->sum('quantity_purchased');
        $this->save();
    }
}
