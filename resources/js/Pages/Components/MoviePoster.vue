<script setup>
import ItemOverlayDetail from "./ItemOverlayDetail.vue";
import ItemPoster from "./ItemPoster.vue";
import ItemStatusIcon from "./ItemStatusIcon.vue";
import { ref } from "vue";

const props = defineProps({
    size: {
        type: String,
        default: "w154",
    },
    movie: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["openMovieModal"]);

const movieDetail = ref(props.movie);

function openMovieModal() {
    emit("openMovieModal", movieDetail.value);
}
</script>

<template>
    <button class="group relative mb-auto block max-w-fit rounded-xl" @click="openMovieModal(movie)" :id="movie.slug">
        <ItemStatusIcon :purchased="movie.purchase_date !== null" />
        <ItemPoster :placeholder="movie.poster_path === null" :poster_path="movie.poster_path" :size="size" />
        <ItemOverlayDetail
            :class="'text-left' + (movie.poster_path === null ? '' : ' invisible group-hover:visible group-focus:visible')"
            :title="movie.title"
            :release_year="movie.release_year"
            :certification="movie.certification"
        />
    </button>
</template>
