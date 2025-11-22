<?php

namespace App\Models;

use App\Models\Item;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasSlug,SoftDeletes,HasFactory,LogsActivity;
    protected $fillable = [
        'slug',
        'name',
        'description',   
        'active'
    ];  

    protected function casts()
    {
        return [
            'active' => 'boolean',
        ];
    }
    
    public function items()
    {
        return $this->hasMany(Item::class);
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
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('category');
    }
}
