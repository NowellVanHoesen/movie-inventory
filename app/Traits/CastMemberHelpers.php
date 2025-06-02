<?php

namespace App\Traits;

use App\Models\CastMember;

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

}
