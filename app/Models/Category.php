<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasSlug,SoftDeletes,HasFactory;
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
}
