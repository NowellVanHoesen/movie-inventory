<?php

namespace App\Traits;

use App\Models\MediaType;

trait MediaTypeHelpers
{
    private function get_media_types()
    {
        $media_types = MediaType::orderBy('parent_id')->orderBy('name')->get();

        $media_types_arr = [];

        foreach ($media_types as $type) {
            if ($type->parent_id === 0) {
                $media_types_arr[$type->name] = $media_types->filter(function ($item) use ($type) {
                    return $type->id === $item->parent_id;
                })->pluck('name', 'id');
            }
        }

        return $media_types_arr;

    }

    private function get_media_types_display($media_types)
    {
        $media_types_display = [];

        if (empty($media_types)) {
            return $media_types_display;
        }

        $media_types_collection = MediaType::all();

        foreach ($media_types as $type) {
            $tmp_media_type_arr = [$type->name];

            $current_parent_id = $type->parent_id;

            while ($current_parent_id !== 0) {
                $media_type_parent = $media_types_collection->firstWhere( 'id', $current_parent_id);
                $tmp_media_type_arr = [$media_type_parent->name => $tmp_media_type_arr];
                $current_parent_id = $media_type_parent->parent_id;
            }

            $media_types_display = array_merge_recursive($media_types_display, $tmp_media_type_arr);
        }

        return $media_types_display;
    }
}