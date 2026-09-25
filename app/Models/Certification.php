<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certification extends Model {
    /**
     * The id of the named certification, falling back to NR for anything not in the
     * table. TMDB returns ratings we don't seed (e.g. "TV-Y7-FV", "Unrated", foreign
     * codes), and a null ->id here used to 500 the whole store request.
     */
    public static function idFor(?string $name): int {
        $names = array_values(array_filter([$name, 'NR']));

        return static::whereIn('name', $names)
            ->orderByRaw("name = 'NR'")
            ->value('id');
    }

    public function series() {
        return $this->hasMany(Series::class);
    }

    public function movie() {
        return $this->hasMany(Movie::class);
    }
}
