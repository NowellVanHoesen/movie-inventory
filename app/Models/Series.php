<?php

namespace App\Models;

use App\Models\CastMember;
use App\Models\Certification;
use App\Models\Genre;
use App\Models\MediaType;
use App\Models\Season;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Series extends Model
{
    /** @use HasFactory<\Database\Factories\SeriesFactory> */
    use HasFactory;

    protected $guarded = [];

    public $incrementing = false;

    public $timestamps = false;

    #[Scope]
    protected function search($query, $term) {
        $term = "%{$term}%";
        $query->where('name', 'like', $term);
    }

    public function seasons()
    {
        return $this->hasMany(Season::class)->orderBy( 'air_date', 'asc' );
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
        return $this->belongsToMany(CastMember::class, table: "cast_member_series", foreignPivotKey: "series_id")->withPivot('character','order')->orderByPivot('order', 'asc');
    }
}
