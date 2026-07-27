<script setup>
import Layout from "../../../layouts/Layout.vue";
import ItemPoster from "../../Components/ItemPoster.vue";
import MoviePoster from "../../Components/MoviePoster.vue";
import MoviePosterPlaceholder from "../../Components/MoviePosterPlaceholder.vue";

const props = defineProps({
    page_title: {
        type: String,
        default: "Movie Collection",
    },
    collection: {
        type: Object,
        required: true,
    },
    collection_details: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Layout :heading="page_title" :backdrop="collection.backdrop_path">
        <div class="mt-6 gap-4 rounded-xl bg-white/50 p-6 text-gray-900 md:grid md:grid-cols-[300px_1fr]">
            <div>
                <ItemPoster :placeholder="collection.poster_path === null" :poster_path="collection.poster_path" size="original" />
            </div>
            <div>
                <h2 class="text-2xl font-bold">{{ collection.name }}</h2>
                <p class="mt-4">{{ collection.overview }}</p>
                <div class="mt-4 grid grid-cols-[repeat(auto-fill,minmax(156px,1fr))] place-items-center gap-4">
                    <template v-for="movie in collection_details.parts">
                        <MoviePoster
                            v-if="movie.slug"
                            :href="route('movies.show', movie)"
                            :movie="movie"
                            :key="movie.slug"
                        />
                        <MoviePosterPlaceholder
                            v-else
                            :href="route('movies.create')"
                            :movie="movie"
                            :key="movie.id"
                        />
                    </template>
                </div>
            </div>
        </div>
    </Layout>
</template>
