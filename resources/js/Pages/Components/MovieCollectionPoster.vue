<script setup>
import ItemOverlayDetail from "./ItemOverlayDetail.vue";
import ItemPoster from "./ItemPoster.vue";
import PosterLink from "./PosterLink.vue";

defineProps({
    size: {
        type: String,
        default: "w154",
    },
    collection: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <PosterLink :href="route('movieCollection.show', collection)">
        <ItemPoster :placeholder="collection.poster_path === null" :poster_path="collection.poster_path" />
        <ItemOverlayDetail
            :class="'text-left' + (collection.poster_path === null ? '' : ' invisible group-hover:visible group-focus:visible')"
            :title="collection.name"
        />
    </PosterLink>
</template>
