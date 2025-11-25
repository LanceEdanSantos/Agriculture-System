<?php

namespace App\Models;

use App\Enums\ItemRequestStatus;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\Activitylog\Traits\LogsActivity;

class ItemRequest extends Model
{
    use SoftDeletes, HasUuids,LogsActivity;
    protected $fillable = [
        'user_id',
        'item_id',
        'quantity',
        'status',
        'farm_id',
        'notes',
    ];
    protected function casts()
    {
        return [
            'quantity' => 'integer',
            'status' => ItemRequestStatus::class,
            'farm_id' => 'integer',
            'notes' => 'string',
        ];
    }

    public function getRouteKeyName()
    {
        return 'id';
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
    public function messages()
    {
        return $this->hasMany(ItemRequestMessage::class)->latest();
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('item_request');
    }

    public function getItemNameAttribute()
    {
        return $this->item->name;
    }
}
