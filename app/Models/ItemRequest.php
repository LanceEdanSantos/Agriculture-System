<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use App\Models\RequestFeedback;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ItemRequest extends Model
{
    use LogsActivity;
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_IN_DELIVERY = 'in_delivery';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'farm_id',
        'inventory_item_id',
        'quantity',
        'notes',
        'status',
        'requested_at',
        'approved_at',
        'delivered_at',
        'approved_by',
        'rejection_reason',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'user_id',
                'farm_id',
                'inventory_item_id',
                'quantity',
                'notes',
                'status',
                'approved_by',
                'rejection_reason',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->dontLogIfAttributesChangedOnly(['updated_at']);
    }

    protected $with = ['statuses'];

    protected static function booted()
    {
        static::creating(function ($itemRequest) {
            if (empty($itemRequest->status)) {
                $itemRequest->status = self::STATUS_PENDING;
            }
            if (empty($itemRequest->requested_at)) {
                $itemRequest->requested_at = now();
            }
            if (empty($itemRequest->user_id) && Auth::check()) {
                $itemRequest->user_id = Auth::id();
            }
        });

        static::created(function ($itemRequest) {
            $itemRequest->statuses()->create([
                'status' => $itemRequest->status,
                'changed_by' => Auth::id(),
                'notes' => 'Request created',
            ]);
        });

        static::updating(function ($itemRequest) {
            if ($itemRequest->isDirty('status')) {
                $itemRequest->statuses()->create([
                    'status' => $itemRequest->status,
                    'changed_by' => Auth::id(),
                    'notes' => 'Status changed from ' . $itemRequest->getOriginal('status') . ' to ' . $itemRequest->status,
                ]);

                // Update timestamps based on status
                if ($itemRequest->status === self::STATUS_APPROVED && !$itemRequest->approved_at) {
                    $itemRequest->approved_at = now();
                    $itemRequest->approved_by = Auth::id();
                } elseif ($itemRequest->status === self::STATUS_DELIVERED && !$itemRequest->delivered_at) {
                    $itemRequest->delivered_at = now();
                }
            }
        });

        // static::updated(function ($itemRequest){
        //     if($itemRequest->wasChanged('status')&& $itemRequest->status === self::STATUS_APPROVED) {
        //         if($itemRequest->inventoryItem){
        //             \App\Models\StockMovement::firstOrCreate([
        //                 'inventory_item_id' => $itemRequest->inventoryItem->id,
        //                 'user_id' => $itemRequest->user_id,
        //                 'type' => 'out',
        //                 'quantity' => $itemRequest->quantity,
        //                 'notes' => $itemRequest->notes,
        //                 'user_id' => Auth::id(),
        //                 'movement_date' => now(),
        //                 'reason' => 'Item request approval',
        //             ],
        //         );
        //         }
        //     }
        // });
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_IN_DELIVERY => 'In Delivery',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_CANCELLED => 'Cancelled',
        ];
    }

    /**
     * Get the display name for the status.
     */
    public function getDisplayStatusAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    /**
     * Get the user who created the request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the farm associated with the request.
     */
    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    /**
     * Get the inventory item associated with the request.
     */
    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    /**
     * Get the user who approved the request.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get all status changes for the request.
     */
    public function statuses(): HasMany
    {
        return $this->hasMany(ItemRequestStatus::class)->latest();
    }

    /**
     * Get the current status record.
     */
    public function currentStatus(): HasOne
    {
        return $this->hasOne(ItemRequestStatus::class)->latestOfMany();
    }

    /**
     * Get all feedback for the request.
     */
    public function feedback(): HasMany
    {
        return $this->hasMany(RequestFeedback::class);
    }

    /**
     * Scope a query to only include pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include approved requests.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope a query to only include delivered requests.
     */
    public function scopeDelivered($query)
    {
        return $query->where('status', self::STATUS_DELIVERED);
    }

    /**
     * Check if the request is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the request is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if the request is delivered.
     */
    public function isDelivered(): bool
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    /**
     * Approve the request.
     */
    public function approve(int $approvedBy, ?string $notes = null): bool
    {
        $wasUpdated = $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $approvedBy,
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        if ($wasUpdated && $this->inventoryItem) {
            // Justin villarin jabolero
            \App\Models\StockMovement::create([
                'inventory_item_id' => $this->inventory_item_id,
                'user_id' => $approvedBy,
                'type' => 'out',
                'quantity' => $this->quantity,
                'unit_cost' => $this->inventoryItem->average_unit_cost ?? 0,
                'total_cost' => ($this->inventoryItem->average_unit_cost ?? 0) * $this->quantity,
                'reason' => 'Item request approval',
                'notes' => $notes ?? 'Automatically created from approved item request #' . $this->id,
                'movement_date' => now(),
            ]);
        }

        return $wasUpdated;
    }


    /**
     * Reject the request.
     */
    public function reject(int $rejectedBy, string $reason, ?string $notes = null): bool
    {
        return $this->update([
            'status' => self::STATUS_REJECTED,
            'rejection_reason' => $reason,
        ]);
    }

    /**
     * Mark the request as in delivery.
     */
    public function markAsInDelivery(int $changedBy, ?string $notes = null): bool
    {
        return $this->update([
            'status' => self::STATUS_IN_DELIVERY,
        ]);
    }

    /**
     * Mark the request as delivered.
     */
    public function markAsDelivered(int $deliveredBy, ?string $notes = null): bool
    {
        return $this->update([
            'status' => self::STATUS_DELIVERED,
            'delivered_at' => now(),
        ]);
    }

    /**
     * Cancel the request.
     */
    public function cancel(int $cancelledBy, ?string $reason = null): bool
    {
        return $this->update([
            'status' => self::STATUS_CANCELLED,
            'rejection_reason' => $reason,
        ]);
    }

    /**
     * Get all attachments for the request.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(ItemRequestAttachment::class);
    }
}
