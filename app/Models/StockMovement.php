<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_item_id',
        'user_id',
        'type',
        'quantity',
        'unit_cost',
        'total_cost',
        'reason',
        'notes',
        'movement_date',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'movement_date' => 'datetime',
    ];

    /**
     * Get the inventory item for this stock movement
     */
    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    /**
     * Get the user who made this stock movement
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get formatted quantity with direction
     */
    public function getFormattedQuantityAttribute(): string
    {
        if ($this->type === 'in') {
            return '+' . $this->quantity;
        }
        return '-' . $this->quantity;
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
     * Scope for stock in movements
     */
    public function scopeStockIn($query)
    {
        return $query->where('type', 'in');
    }

    /**
     * Scope for stock out movements
     */
    public function scopeStockOut($query)
    {
        return $query->where('type', 'out');
    }
}