<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class RequestMessage extends Model
{
    use LogsActivity;

    protected $fillable = [
        'item_request_id',
        'user_id',
        'message',
        'is_admin_message',
        'read_at',
    ];

    protected $casts = [
        'is_admin_message' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'item_request_id',
                'user_id',
                'message',
                'is_admin_message',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->dontLogIfAttributesChangedOnly(['updated_at', 'read_at']);
    }

    protected static function booted()
    {
        static::creating(function ($message) {
            if (empty($message->user_id) && Auth::check()) {
                $message->user_id = Auth::id();
            }
            
            // Auto-detect if message is from admin
            if (empty($message->is_admin_message) && Auth::check()) {
                $user = Auth::user();
                $message->is_admin_message = $user->hasRole('super_admin') || $user->hasRole('farm_manager');
            }
        });
    }

    public function itemRequest(): BelongsTo
    {
        return $this->belongsTo(ItemRequest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): void
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
