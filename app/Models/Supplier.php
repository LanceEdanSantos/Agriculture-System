<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Supplier extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'company_name',
        'contact_persons',
        'phone_numbers',
        'email_addresses',
        'address',
        'website',
        'tax_id',
        'business_license',
        'notes',
        'status',
    ];

    protected $casts = [
        'contact_persons' => 'array',
        'phone_numbers' => 'array',
        'email_addresses' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'company_name',
                'contact_persons',
                'phone_numbers',
                'email_addresses',
                'address',
                'website',
                'tax_id',
                'business_license',
                'notes',
                'status',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the inventory items supplied by this supplier
     */
    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class);
    }

    /**
     * Get the purchase history for this supplier
     */
    public function purchaseHistory()
    {
        return $this->hasMany(PurchaseHistory::class);
    }

    /**
     * Get primary contact person
     */
    public function getPrimaryContactAttribute()
    {
        if (!$this->contact_persons) {
            return null;
        }
        return $this->contact_persons[0] ?? null;
    }

    /**
     * Get primary phone number
     */
    public function getPrimaryPhoneAttribute()
    {
        if (!$this->phone_numbers) {
            return null;
        }
        return $this->phone_numbers[0] ?? null;
    }

    /**
     * Get primary email
     */
    public function getPrimaryEmailAttribute()
    {
        if (!$this->email_addresses) {
            return null;
        }
        return $this->email_addresses[0] ?? null;
    }

    /**
     * Check if supplier is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get formatted contact information
     */
    public function getFormattedContactsAttribute(): string
    {
        $contacts = [];

        if ($this->contact_persons) {
            $contacts[] = 'Contacts: ' . implode(', ', $this->contact_persons);
        }

        if ($this->phone_numbers) {
            $contacts[] = 'Phones: ' . implode(', ', $this->phone_numbers);
        }

        if ($this->email_addresses) {
            $contacts[] = 'Emails: ' . implode(', ', $this->email_addresses);
        }

        return implode(' | ', $contacts);
    }
}
