<script setup>
import ItemOverlayDetail from "./ItemOverlayDetail.vue";
import ItemPoster from "./ItemPoster.vue";
import ItemStatusIcon from "./ItemStatusIcon.vue";
import { open } from "@/useModal.js";

const props = defineProps({
    size: {
        type: String,
        default: "w185",
    },
    movie: {
        type: Object,
        required: true,
    },
    href: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <form :action="href">
        <input type="hidden" name="movie_id" :value="movie.id" />
        <button
            type="button"
            class="group relative mb-auto block overflow-hidden rounded-xl"
            :class="size === w185 ? 'max-w-[187px]' : 'max-w-auto'"
            @click="open(href)"
            :dusk="`movie-btn-${movie.id}-placeholder`"
        >
            <ItemPoster
                :placeholder="movie.poster_path === null"
                :poster_path="movie.poster_path"
                :size="size"
                class="group-hover:opacity-60"
            />
            <ItemOverlayDetail
                :class="
                    'text-left' + (movie.poster_path === null ? '' : ' invisible group-hover:visible group-focus:visible')
                "
                :title="movie.title"
                :release_year="movie.release_year"
                :certification="movie.certification"
            />
        </button>
    </form>
</template>
