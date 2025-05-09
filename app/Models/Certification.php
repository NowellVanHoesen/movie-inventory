<?php

namespace App\Models;

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
