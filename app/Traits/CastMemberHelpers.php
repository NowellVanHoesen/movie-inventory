<?php

namespace App\Traits;

use App\Models\CastMember;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Season;
use App\Models\Series;

trait CastMemberHelpers
{
    private function getCastMember($cast_member)
    {
        return CastMember::firstOrCreate(
            ['id' => $cast_member->id],
            [
                'name' => $cast_member->name,
                'original_name' => $cast_member->original_name,
                'profile_path' => $cast_member->profile_path ?: null,
            ]
        );
    }

    private function attachCastMemberToModel( $model, $cast_members ) {
       foreach ($cast_members as $cast_member) {
            if ( $model->cast_members()->where('cast_member_id', $cast_member->id)->exists() ) {
                continue;
            }

            $member = $this->getCastMember($cast_member);

            $model->cast_members()->attach($member->id, ['character' => $cast_member->character, 'order' => $cast_member->order]);
        }
    }

}
