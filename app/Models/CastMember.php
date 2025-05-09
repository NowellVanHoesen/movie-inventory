<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CastMember extends Model
{
    /** @use HasFactory<\Database\Factories\CastMemberFactory> */
    use HasFactory;

    protected $fillable = ['id', 'name', 'original_name', 'profile_path'];

    public $incrementing = false;

    public $timestamps = false;

    public function movies()
    {
        return $this->belongsToMany(Movie::class, relatedPivotKey: 'movie_id')->withPivot('character');
    }

    public function series()
    {
        return $this->belongsToMany(Series::class);
    }

    public function seasons()
    {
        return $this->belongsToMany(Season::class);
    }

    public function episodes()
    {
        return $this->belongsToMany(Episode::class);
    }
}
