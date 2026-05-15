<script setup>
import ItemOverlayDetail from "./ItemOverlayDetail.vue";
import ItemPoster from "./ItemPoster.vue";
import ItemStatusIcon from "./ItemStatusIcon.vue";
import { ref } from "vue";
import { open } from "@/useModal.js";

const props = defineProps({
    size: {
        type: String,
        default: "w154",
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

const movieDetail = ref(props.movie);
</script>

<template>
    <button type="button" class="group relative mb-auto block max-w-fit rounded-xl" @click="open(href)" :id="movie.slug">
        <ItemStatusIcon :purchased="movie.purchase_date !== null" />
        <ItemPoster
            :placeholder="movie.poster_path === null"
            :poster_path="movie.poster_path"
            :size="size"
            class="group-hover:opacity-60"
        />
        <ItemOverlayDetail
            :class="'text-left' + (movie.poster_path === null ? '' : ' invisible group-hover:visible group-focus:visible')"
            :title="movie.title"
            :release_year="movie.release_year"
            :certification="movie.certification"
        />
    </button>
</template>
