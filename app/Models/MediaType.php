<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaType extends Model
{
    /** @use HasFactory<\Database\Factories\MediaTypeFactory> */
    use HasFactory;

    protected $fillable = ['name', 'parent_id'];

    public $timestamps = false;

    public function movies()
    {
        return $this->belongsToMany(Movie::class);
    }

    public function series()
    {
        return $this->belongsToMany(Series::class);
    }

    public function seasons()
    {
        return $this->belongsToMany(Season::class);
    }
}
