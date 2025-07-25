<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Movie extends Model
{
    use HasSlug;

    protected $guarded = [];

    public $incrementing = false;

    public $timestamps = false;

    #[Scope]
    protected function purchased(Builder $query)
    {
        $query->whereNotNull('purchase_date');
    }

    #[Scope]
    protected function wishlist(Builder $query)
    {
        $query->whereNull('purchase_date');
    }

    #[Scope]
    protected function search($query, $term)
    {
        $term = "%{$term}%";
        $query->where('title', 'like', $term);
    }

    public function certification()
    {
        return $this->belongsTo(Certification::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }

    public function media_types()
    {
        return $this->belongsToMany(MediaType::class);
    }

    public function cast_members()
    {
        return $this->belongsToMany(CastMember::class, foreignPivotKey: 'movie_id')->withPivot('character', 'order')->orderByPivot('order', 'asc');
    }

    public function collection()
    {
        return $this->belongsTo(MovieCollection::class);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
