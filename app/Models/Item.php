<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\ItemRequestMessage;

class Item extends Model
{
    use HasSlug, SoftDeletes, HasFactory,LogsActivity;
    protected $fillable = [
        'slug',
        'name',
        'category_id',
        'stock',
        'minimum_stock',
        'description',
        'notes',
        'active'
    ];

    protected function casts()
    {
        return [
            'active' => 'boolean',
            'stock' => 'integer',
            'notes' => 'array',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function itemRequestMessages()
    {
        return $this->hasMany(ItemRequestMessage::class, 'item_id');
    }

    public function farms()
    {
        return $this->belongsToMany(Farm::class)->withTimestamps();
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('item');
    }   
}
