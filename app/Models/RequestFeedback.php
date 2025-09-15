<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestFeedback extends Model
{
    public const TYPE_COMMENT = 'comment';
    public const TYPE_ISSUE = 'issue';
    public const TYPE_COMPLAINT = 'complaint';
    public const TYPE_PRAISE = 'praise';

    protected $fillable = [
        'item_request_id',
        'user_id',
        'feedback',
        'type',
        'resolved',
        'resolution_notes',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'resolved' => 'boolean',
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function types(): array
    {
        return [
            self::TYPE_COMMENT => 'Comment',
            self::TYPE_ISSUE => 'Issue',
            self::TYPE_COMPLAINT => 'Complaint',
            self::TYPE_PRAISE => 'Praise',
        ];
    }

    public function itemRequest(): BelongsTo
    {
        return $this->belongsTo(ItemRequest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function markAsResolved(User $resolver, ?string $notes = null): void
    {
        $this->update([
            'resolved' => true,
            'resolved_by' => $resolver->id,
            'resolved_at' => now(),
            'resolution_notes' => $notes,
        ]);
    }
}
