<script setup>
import Layout from "@/Layouts/Layout.vue";
import MoviePoster from "../Components/MoviePoster.vue";
import SeriesPoster from "../Components/SeriesPoster.vue";
import MovieCollectionPoster from "../Components/MovieCollectionPoster.vue";

defineProps({
    movies: {
        type: Array,
        required: true,
    },
    collections: {
        type: Array,
        required: true,
    },
    series: {
        type: Array,
        required: true,
    },
    search: {
        type: String,
        default: "",
    },
    page_title: {
        type: String,
        default: "Search Results",
    },
});
</script>

<template>
    <Layout :heading="page_title">
        <h2 class="text-2xl">Movies</h2>
        <div class="mt-2 grid grid-cols-[repeat(auto-fill,minmax(187px,1fr))] place-items-center gap-4">
            <p v-if="!movies.length" dusk="search-movies-empty">No Results Found</p>
            <MoviePoster v-for="movie in movies" :key="movie.slug" :movie="movie" :href="route('movies.show', movie)" />
        </div>
        <h2 class="mt-6 text-2xl">Movie Collections</h2>
        <div class="mt-2 grid grid-cols-[repeat(auto-fill,minmax(187px,1fr))] place-items-center gap-4">
            <p v-if="!collections.length" dusk="search-collections-empty">No Results Found</p>
            <MovieCollectionPoster v-for="collection in collections" :key="collection.slug" :collection="collection" />
        </div>
        <h2 class="mt-6 text-2xl">Series</h2>
        <div class="mt-2 grid grid-cols-[repeat(auto-fill,minmax(187px,1fr))] place-items-center gap-4">
            <p v-if="!series.length" dusk="search-series-empty">No Results Found</p>
            <SeriesPoster v-for="show in series" :key="show.slug" :series="show" />
        </div>
    </Layout>
</template>
