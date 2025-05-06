<?php

namespace App\Models;

use App\Models\Season;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    /** @use HasFactory<\Database\Factories\EpisodeFactory> */
    use HasFactory;

    protected $guarded = [];

    public $incrementing = false;

    public $timestamps = false;

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function cast_members()
    {
        return $this->belongsToMany(CastMember::class, table: "cast_member_episode", foreignPivotKey: "episode_id")->withPivot('character','order')->orderByPivot('order', 'asc');
    }}
