<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class CastMember extends Model
{
    use HasSlug;

    protected $fillable = ['id', 'name', 'original_name', 'profile_path'];

    public $incrementing = false;

    public $timestamps = false;

    public function movies()
    {
        return $this->belongsToMany(Movie::class, relatedPivotKey: 'movie_id')->withPivot('character');
    }

    public function series()
    {
        return $this->belongsToMany(Series::class)->withPivot('character', 'order');
    }

    public function seasons()
    {
        return $this->belongsToMany(Season::class)->withPivot('character', 'order');
    }

    public function episodes()
    {
        return $this->belongsToMany(Episode::class)->withPivot('character', 'order');
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
