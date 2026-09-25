<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Series extends Model {
    use HasSlug;

    protected $guarded = [];

    public $incrementing = false;

    protected $with = ['certification'];

    public $timestamps = false;

    public function seasons() {
        return $this->hasMany(Season::class)->orderBy('air_date', 'asc');
    }

    public function episodes() {
        return $this->hasManyThrough(Episode::class, Season::class)->orderBy('air_date', 'asc');
    }

    public function certification() {
        return $this->belongsTo(Certification::class);
    }

    public function genres() {
        return $this->belongsToMany(Genre::class);
    }

    public function mediaTypes() {
        return $this->belongsToMany(MediaType::class);
    }

    public function castMembers() {
        return $this->belongsToMany(CastMember::class, table: 'cast_member_series', foreignPivotKey: 'series_id')->withPivot('character', 'order')->orderByPivot('order', 'asc');
    }

    public function getSlugOptions(): SlugOptions {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName() {
        return 'slug';
    }
}
