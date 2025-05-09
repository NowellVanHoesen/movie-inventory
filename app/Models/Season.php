<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    /** @use HasFactory<\Database\Factories\SeasonFactory> */
    use HasFactory;

    protected $fillable = [
        'id',
        '_id',
        'series_id',
        'imdb_id',
        'name',
        'overview',
        'air_date',
        'purchase_date',
        'season_number',
        'poster_path',
    ];

    public $incrementing = false;

    public $timestamps = false;

    public function series()
    {
        return $this->belongsTo(Series::class);
    }

    public function episodes()
    {
        return $this->hasMany(Episode::class)->orderBy('episode_number', 'asc');
    }

    public function media_types()
    {
        return $this->belongsToMany(MediaType::class);
    }

    public function cast_members()
    {
        return $this->belongsToMany(CastMember::class, table: 'cast_member_season', foreignPivotKey: 'season_id')->withPivot('character', 'order')->orderByPivot('order', 'asc');
    }
}
