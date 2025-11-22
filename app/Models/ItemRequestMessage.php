<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemRequestMessage extends Model
{
    protected $fillable = [
        'item_request_id',
        'user_id',
        'message',
    ];

    public function request()
    {
        return $this->belongsTo(ItemRequest::class, 'item_request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
