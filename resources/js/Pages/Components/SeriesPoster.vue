<script setup>
import ItemOverlayDetail from "./ItemOverlayDetail.vue";
import ItemPoster from "./ItemPoster.vue";
import ItemStatusIcon from "./ItemStatusIcon.vue";
import PosterLink from "./PosterLink.vue";

defineProps({
    series: {
        type: Object,
        required: true,
    },
    size: {
        type: String,
        default: "w185",
    },
});
</script>

<template>
    <PosterLink :href="route('series.show', series)" :id="series.id">
        <ItemStatusIcon :purchased="series.purchase_date !== null" />
        <ItemPoster :placeholder="series.poster_path == null" :poster_path="series.poster_path" :size="size" />
        <ItemOverlayDetail
            :class="'text-left' + (series.poster_path === null ? '' : ' invisible group-hover:visible group-focus:visible')"
            :title="series.name"
            :release_year="series.first_air_date ? series.first_air_date : 'TBA'"
        />
    </PosterLink>
</template>
