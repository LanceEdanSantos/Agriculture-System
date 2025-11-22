<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Unit extends Model
{
    use LogsActivity;
    
    protected $fillable = ['name', 'symbol', 'description'];

    
    public function items()
    {
        return $this->hasMany(Item::class, 'unit_id');
    }
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('unit');
    }

    public function getItemsCountAttribute()
    {
        return $this->items()->count();
    }
}
