<?php

namespace App\Models;

use App\Enums\TransferType;
use App\Observers\StockLogObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([StockLogObserver::class])]
class StockLog extends Model
{
    use HasUuids;
    protected $fillable = [
        'item_id',
        'user_id',
        'uuid',
        'full_name',
        'quantity',
        'type',
        'notes',
    ];

    protected function casts(): array {
        return [
            'quantity' => 'integer',
            'type' => 'string',
            'notes' => 'string',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
