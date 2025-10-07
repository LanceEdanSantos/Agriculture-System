<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_request_id',
        'inventory_item_id',
        'category',
        'note',
        'item_no',
        'item_code',
        'description',
        'unit',
        'quantity',
        'unit_cost',
        'total_cost',
        'category_total',
        'is_custom_item',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'category_total' => 'decimal:2',
    ];

    /**
     * Get the purchase request that owns this item
     */
    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    /**
     * Get the inventory item for this purchase request item
     */
    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    /**
     * Get formatted unit cost
     */
    public function getFormattedUnitCostAttribute(): string
    {
        return '₱' . number_format($this->unit_cost, 2);
    }

    /**
     * Get formatted total cost
     */
    public function getFormattedTotalCostAttribute(): string
    {
        return '₱' . number_format($this->total_cost, 2);
    }

    /**
     * Get formatted category total
     */
    public function getFormattedCategoryTotalAttribute(): string
    {
        return '₱' . number_format($this->category_total, 2);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (empty($item->category)) {
                $item->category = Str::random(8); // random 8-character string
            }
        });
    }
}
