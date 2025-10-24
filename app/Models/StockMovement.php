<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;

class StockMovement extends Model
{
    use HasFactory, SoftDeletes;

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
        'deleted_at' => 'datetime',
    ];

    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults()
    //         ->logOnly([
    //             'inventory_item_id',
    //             'user_id',
    //             'type',
    //             'quantity',
    //             'unit_cost',
    //             'total_cost',
    //             'reason',
    //             'notes',
    //             'movement_date',
    //         ])
    //         ->logOnlyDirty()
    //         ->dontSubmitEmptyLogs()
    //         ->dontLogIfAttributesChangedOnly(['updated_at']);
    // }

    protected static function booted()
    {
        // When new stock movement is created
        static::created(function (StockMovement $movement) {
            self::applyStockChange($movement);
        });

        // When an existing stock movement is updated
        static::updated(function (StockMovement $movement) {
            $original = $movement->getOriginal();
            $item = $movement->inventoryItem;

            if (! $item) {
                return;
            }

            // Revert previous stock impact
            if ($original['type'] === 'in') {
                $item->current_stock -= $original['quantity'];
            } else {
                $item->current_stock += $original['quantity'];
            }

            $item->save();

            // Apply the new movement changes
            self::applyStockChange($movement);
        });
    }

    /**
     * Apply stock adjustment logic (shared by create and update events)
     */
    protected static function applyStockChange(StockMovement $movement)
    {
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
            // For stock out, ensure we don't go negative
            $availableStock = $item->getAvailableStockForOut();
            $originalQuantity = $movement->quantity;

            if ($movement->quantity > $availableStock) {
                // Adjust to available stock
                $movement->quantity = $availableStock;
                if ($movement->unit_cost) {
                    $movement->total_cost = $movement->unit_cost * $movement->quantity;
                }

                Log::info("Stock movement quantity adjusted", [
                    'movement_id' => $movement->id,
                    'item_name' => $item->name,
                    'requested_quantity' => $originalQuantity,
                    'adjusted_quantity' => $movement->quantity,
                    'available_stock' => $availableStock,
                    'user_id' => $movement->user_id
                ]);

                Notification::make()
                    ->title('Stock Quantity Adjusted')
                    ->body("Requested quantity ({$originalQuantity}) exceeded available stock. Adjusted to available amount ({$movement->quantity}) for {$item->name}.")
                    ->warning()
                    ->sendToDatabase($movement->user);
            }

            if ($movement->quantity > 0) {
                $item->current_stock = ($item->current_stock ?? 0) - $movement->quantity;
            }
        }

        // Update average cost for stock-in
        if ($movement->type === 'in' && $movement->quantity > 0) {
            $prevAvg = $item->average_unit_cost ?? 0;
            $prevTotalQty = $item->total_purchased ?? 0;

            $newTotalCost = ($prevAvg * $prevTotalQty) + ($movement->total_cost ?? 0);
            $newTotalQty = $prevTotalQty + $movement->quantity;

            $item->average_unit_cost = $newTotalQty > 0 ? ($newTotalCost / $newTotalQty) : $prevAvg;
            $item->total_purchased = $newTotalQty;
        }

        $item->save();

        // Notify all admins
        $admins = User::role('super_admin')->get();
        foreach ($admins as $admin) {
            Notification::make()
                ->title('Stock Movement Recorded')
                ->body("{$movement->quantity} {$item->unit?->name} of {$item->name} ({$movement->type}) by {$movement->user?->name}")
                ->success()
                ->sendToDatabase($admin);
        }

        // Low stock warning
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
    }

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedQuantityAttribute(): string
    {
        return $this->type === 'in'
            ? '+' . $this->quantity
            : '-' . $this->quantity;
    }

    public function getFormattedUnitCostAttribute(): string
    {
        return '₱' . number_format($this->unit_cost, 2);
    }

    public function getFormattedTotalCostAttribute(): string
    {
        return '₱' . number_format($this->total_cost, 2);
    }

    public function scopeStockIn($query)
    {
        return $query->where('type', 'in');
    }

    public function scopeStockOut($query)
    {
        return $query->where('type', 'out');
    }
}
