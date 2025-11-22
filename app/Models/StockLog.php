<?php

namespace App\Models;

use App\Enums\TransferType;
use App\Observers\StockLogObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
#[ObservedBy([StockLogObserver::class])]
class StockLog extends Model
{
    use HasUuids, SoftDeletes,LogsActivity;
    protected $fillable = [
        'item_id',
        'user_id',
        'uuid',
        'full_name',
        'quantity',
        'type',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'type' => 'string',
            'notes' => 'string',
            'deleted_at' => 'datetime',
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
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('stock_log');
    }
}
