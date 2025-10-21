<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PurchaseHistory extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'purchase_history';

    protected $fillable = [
        'inventory_item_id',
        'purchase_request_id',
        'supplier_id',
        'item_description',
        'category',
        'unit',
        'quantity_purchased',
        'unit_cost',
        'total_cost',
        'purchase_date',
        'delivery_date',
        'expiration_date',
        'purchase_order_number',
        'invoice_number',
        'status',
        'notes',
        'received_by',
        'received_at',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'delivery_date' => 'date',
        'expiration_date' => 'date',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'received_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'inventory_item_id',
                'purchase_request_id',
                'supplier_id',
                'quantity_purchased',
                'unit_cost',
                'total_cost',
                'purchase_date',
                'delivery_date',
                'expiration_date',
                'status',
                'received_by',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->dontLogIfAttributesChangedOnly(['updated_at']);
    }

    /**
     * Get the inventory item
     */
    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    /**
     * Get the purchase request
     */
    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    /**
     * Get the supplier
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
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
     * Check if purchase is delivered
     */
    public function isDelivered(): bool
    {
        return in_array($this->status, ['delivered', 'received']);
    }

    /**
     * Check if purchase is received
     */
    public function isReceived(): bool
    {
        return $this->status === 'received';
    }

    /**
     * Check if purchase is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
}
