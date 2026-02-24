<script setup>
import Layout from "@/Layouts/Layout.vue";
import MoviePoster from "./Components/MoviePoster.vue";
import SeriesPoster from "./Components/SeriesPoster.vue";
import { ref } from "vue";
import MovieModal from "./Components/MovieModal.vue";

defineProps({
    movies: {
        type: Array,
        required: true,
    },
    series: {
        type: Array,
        required: true,
    },
});

const movieDetail = ref(false);

function openMovieModal(movie) {
    movieDetail.value = movie;
}

function closeMovieModal() {
    movieDetail.value = false;
}
</script>

<template>
    <Layout heading="Home Page">
        <h2 class="text-2xl">Latest Movies</h2>
        <div class="mt-4 grid grid-cols-[repeat(auto-fill,minmax(156px,1fr))] place-items-center gap-1 sm:gap-6">
            <MoviePoster v-for="movie in movies" :movie="movie" :key="movie.id" @openMovieModal="openMovieModal" />
        </div>

        <h2 class="mt-6 text-2xl">Latest Series</h2>
        <div class="mt-4 grid grid-cols-[repeat(auto-fill,minmax(156px,1fr))] place-items-center gap-1 sm:gap-6">
            <SeriesPoster v-for="show in series" :series="show" :key="show.id" />
        </div>
    </Layout>
    <MovieModal :show="movieDetail !== false" @close="closeMovieModal" :movie="movieDetail" />
</template>
