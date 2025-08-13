<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'province',
        'lgu',
        'responsibility_center',
        'account_code',
        'department',
        'pr_no',
        'sai_no',
        'date',
        'grand_total',
        'delivery_place',
        'delivery_date_terms',
        'prepared_by',
        'certified_by',
        'requested_by',
        'approved_by',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'grand_total' => 'decimal:2',
        'lgu' => 'boolean',
        'approved_by' => 'array',
    ];

    /**
     * Get the items for this purchase request
     */
    public function items()
    {
        return $this->hasMany(PurchaseRequestItem::class);
    }

    /**
     * Get formatted grand total
     */
    public function getFormattedGrandTotalAttribute(): string
    {
        return '₱' . number_format($this->grand_total, 2);
    }

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->date->format('F d, Y');
    }

    /**
     * Check if purchase request is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if purchase request is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if purchase request is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
