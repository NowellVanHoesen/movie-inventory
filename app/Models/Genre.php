<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model {
    public $incrementing = false;

    public $timestamps = false;

    public function movies() {
        return $this->belongsToMany(Movie::class);
    }

    public function series() {
        return $this->belongsToMany(Series::class);
    }
}
