<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovieCollection extends Model
{
    protected $guarded = [];

    public $incrementing = false;

    public $timestamps = false;

    public function movies()
    {
        return $this->hasMany(Movie::class, 'collection_id');
    }
}
