<?php

namespace App\Models;

use App\Models\Movie;
use App\Models\Series;
use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    public function series()
    {
        return $this->hasMany(Series::class);
    }

    public function movie()
    {
        return $this->hasMany(Movie::class);
    }
}
