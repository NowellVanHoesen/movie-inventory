<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class MovieCollection extends Model
{
    use HasSlug;

    protected $guarded = [];

    public $incrementing = false;

    public $timestamps = false;

    public function movies()
    {
        return $this->hasMany(Movie::class, 'collection_id');
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
