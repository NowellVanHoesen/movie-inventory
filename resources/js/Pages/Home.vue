<script setup>
import Layout from "@/Layouts/Layout.vue";
import MoviePoster from "./Components/MoviePoster.vue";
import SeriesPoster from "./Components/SeriesPoster.vue";
import { ref } from "vue";

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

</script>

<template>
    <Layout heading="Home Page">
        <h2 class="text-2xl">Latest Movies</h2>
        <div class="mt-4 grid grid-cols-[repeat(auto-fill,minmax(187px,1fr))] place-items-center gap-4">
            <MoviePoster
                v-for="movie in movies"
                :href="route('movies.show', movie)"
                :movie="movie"
                :key="movie.slug"
            />
        </div>

        <h2 class="mt-6 text-2xl">Latest Series</h2>
        <div class="mt-4 grid grid-cols-[repeat(auto-fill,minmax(187px,1fr))] place-items-center gap-4">
            <SeriesPoster v-for="show in series" :series="show" :key="show.slug" />
        </div>
    </Layout>
</template>
