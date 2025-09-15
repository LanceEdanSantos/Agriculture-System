<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
    protected static function booted()
    {
        static::created(function (StockMovement $movement) {
            $item = $movement->inventoryItem;

            if (! $item) {
                return;
            }

            // Adjust stock
            if ($movement->type === 'in') {
                $item->current_stock = ($item->current_stock ?? 0) + $movement->quantity;
                $item->last_purchase_date = $movement->movement_date ?? now();
                $item->last_supplier = $movement->user?->name;
            } else {
                $item->current_stock = ($item->current_stock ?? 0) - $movement->quantity;
            }

            // Update average cost (only for stock in)
            if ($movement->type === 'in' && $movement->quantity > 0) {
                $prevAvg = $item->average_unit_cost ?? 0;
                $prevTotalQty = $item->total_purchased ?? 0;

                $newTotalCost = ($prevAvg * $prevTotalQty) + ($movement->total_cost ?? 0);
                $newTotalQty = $prevTotalQty + $movement->quantity;

                $item->average_unit_cost = $newTotalQty > 0 ? ($newTotalCost / $newTotalQty) : $prevAvg;
                $item->total_purchased = $newTotalQty;
            }

            $item->save();

            // Notify all admins about the movement
            $admins = User::role('super_admin')->get();
            foreach ($admins as $admin) {
                Notification::make()
                    ->title('Stock Movement Recorded')
                    ->body("{$movement->quantity} {$item->unit?->name} of {$item->name} ({$movement->type}) by {$movement->user?->name}")
                    ->success()
                    ->sendToDatabase($admin);
            }

            // If item is low after update, notify all super_admins
            if ($item->isLowStock()) {
                $superAdmins = User::role('super_admin')->get();
                foreach ($superAdmins as $superAdmin) {
                    Notification::make()
                        ->title('Low Stock Alert')
                        ->body("{$item->name} is running low! Current stock: {$item->current_stock} {$item->unit?->name}")
                        ->warning()
                        ->sendToDatabase($superAdmin);
                }
            }
        });
    }
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