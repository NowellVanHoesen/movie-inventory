<script setup>
import Layout from "@/Layouts/Layout.vue";
import ItemPoster from "../Components/ItemPoster.vue";
import ItemOverlayDetail from "../Components/ItemOverlayDetail.vue";
import ItemStatusIcon from "../Components/ItemStatusIcon.vue";
import FormButton from "../Components/FormButton.vue";
import { router, useForm } from "@inertiajs/vue3";
import { open } from "@/useModal.js";
import { computed } from "vue";

const props = defineProps({
    page_title: {
        type: String,
        default: "Add Movie",
    },
    search_term: {
        type: String,
        default: "",
    },
    search_year: {
        type: String,
        default: null,
    },
    search_results: {
        type: Object,
        default: null,
    },
    local_results: {
        type: Array,
        default: () => [],
    },
    movie: {
        type: Object,
        default: null,
    },
    media_types: {
        type: Object,
        default: () => ({}),
    },
});

const searchForm = useForm({
    query: props.search_term ?? "",
    year: props.search_year ?? "",
});

const submitSearch = () => {
    searchForm.get(route("movies.create"), {
        preserveState: true,
    });
};

const localMovieFor = (tmdbId) => props.local_results.find((localMovie) => localMovie.id === tmdbId);

const releaseYear = (releaseDate) => (releaseDate ? releaseDate.slice(0, 4) : "TBA");

// Not yet in the local database — send the user back through this same page
// (GET) with `movie_id` set, so the controller fetches and shows TMDB detail.
const selectMovie = (tmdbId) => {
    router.get(route("movies.create"), {
        movie_id: tmdbId,
        search_term: props.search_term,
    });
};

const certification = computed(() => {
    const usRelease = props.movie?.release_dates?.results?.find((result) => result.iso_3166_1 === "US");

    return usRelease?.release_dates?.[0]?.certification ?? "";
});

const genres = computed(() => (props.movie?.genres ?? []).join(" | "));

const saveForm = useForm({
    movie_id: props.movie?.id ?? null,
    purchase_date: "",
    media_type: [],
});

const submitSave = () => {
    saveForm.post(route("movies.store"));
};
</script>

<template>
    <Layout :heading="page_title" :backdrop="movie?.backdrop_path">
        <form class="mx-auto max-w-xl" @submit.prevent="submitSearch">
            <div class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-4">
                <div class="sm:col-span-2">
                    <label for="query" class="block text-sm font-bold">Title Search</label>
                    <input
                        id="query"
                        type="text"
                        v-model="searchForm.query"
                        minlength="2"
                        required
                        autofocus
                        dusk="movie-search-query-input"
                        class="border-cold-steel-300 mt-1 w-full rounded-md border px-2 py-1 text-sm text-gray-900"
                    />
                    <p v-if="searchForm.errors.query" class="mt-1 text-sm text-red-600">{{ searchForm.errors.query }}</p>
                </div>
                <div>
                    <label for="year" class="block text-sm font-bold">Year</label>
                    <input
                        id="year"
                        type="text"
                        v-model="searchForm.year"
                        dusk="movie-search-year-input"
                        class="border-cold-steel-300 mt-1 w-full rounded-md border px-2 py-1 text-sm text-gray-900"
                    />
                    <p v-if="searchForm.errors.year" class="mt-1 text-sm text-red-600">{{ searchForm.errors.year }}</p>
                </div>
                <div class="flex items-end">
                    <FormButton :disabled="searchForm.processing" dusk="movie-search-submit-btn">Search</FormButton>
                </div>
            </div>
        </form>

        <div v-if="search_results" class="mt-6 grid grid-cols-[repeat(auto-fill,minmax(154px,1fr))] gap-5">
            <template v-for="result in search_results.results" :key="result.id">
                <button
                    v-if="result.release_date && localMovieFor(result.id)"
                    type="button"
                    class="group relative mb-auto block max-w-fit overflow-hidden rounded-xl"
                    :dusk="`movie-search-result-${result.id}`"
                    @click="open(route('movies.show', localMovieFor(result.id).slug))"
                >
                    <ItemStatusIcon :purchased="localMovieFor(result.id).purchase_date !== null" />
                    <ItemPoster :placeholder="!result.poster_path" :poster_path="result.poster_path" size="w154" />
                    <ItemOverlayDetail
                        class="text-left invisible group-hover:visible group-focus:visible"
                        :title="result.title"
                        :release_year="releaseYear(result.release_date)"
                    />
                </button>
                <button
                    v-else-if="result.release_date"
                    type="button"
                    class="group relative mb-auto block max-w-fit overflow-hidden rounded-xl"
                    :dusk="`movie-search-result-${result.id}`"
                    @click="selectMovie(result.id)"
                >
                    <ItemPoster :placeholder="!result.poster_path" :poster_path="result.poster_path" size="w154" />
                    <ItemOverlayDetail
                        :class="result.poster_path ? 'text-left invisible group-hover:visible group-focus:visible' : 'text-left'"
                        :title="result.title"
                        :release_year="releaseYear(result.release_date)"
                    />
                </button>
            </template>
            <p v-if="search_results.results.length === 0" class="text-cold-steel-100">No results found.</p>
        </div>

        <div v-else-if="movie" class="mt-6 gap-4 rounded-xl bg-white/80 p-6 text-gray-900 md:grid md:grid-cols-[300px_1fr]">
            <div>
                <img :src="`https://image.tmdb.org/t/p/w300${movie.poster_path}`" :alt="`${movie.title} movie poster`" />
            </div>
            <div class="sm:flex sm:flex-col sm:justify-between">
                <div>
                    <h2 class="text-2xl">
                        {{ movie.title }}
                        <span class="text-sm font-normal">( {{ certification }} ) {{ movie.runtime }} min.</span>
                    </h2>
                    <p v-if="movie.title !== movie.original_title">
                        <strong>Original Title</strong>: ( {{ movie.original_title }} )
                    </p>
                    <p><em>{{ movie.tagline }}</em></p>
                    <p class="text-sm">{{ genres }}</p>
                    <p class="text-sm font-normal">{{ movie.release_date }}</p>
                    <p class="mt-4">{{ movie.overview }}</p>
                </div>
                <form class="m-4" @submit.prevent="submitSave">
                    <div class="grid grid-flow-col grid-cols-2 gap-12">
                        <div>
                            <label for="purchase_date" class="block text-sm font-bold">Date Purchased</label>
                            <input
                                type="date"
                                id="purchase_date"
                                v-model="saveForm.purchase_date"
                                dusk="purchase-date-input"
                                class="border-cold-steel-300 mt-1 rounded-md border px-2 py-1 text-sm"
                            />
                            <p v-if="saveForm.errors.purchase_date" class="mt-1 text-sm text-red-600">
                                {{ saveForm.errors.purchase_date }}
                            </p>
                        </div>
                        <div>
                            <span class="block text-sm font-bold">Media Type</span>
                            <div class="grid sm:grid-cols-2">
                                <div v-for="(subTypes, parent) in media_types" :key="parent">
                                    <span class="text-sm font-bold">{{ parent }}</span>
                                    <ul>
                                        <li v-for="(name, id) in subTypes" :key="id">
                                            <label class="flex items-center gap-1 text-sm">
                                                <input
                                                    type="checkbox"
                                                    :value="Number(id)"
                                                    :dusk="`media-type-${id}`"
                                                    v-model="saveForm.media_type"
                                                />
                                                {{ name }}
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <p v-if="saveForm.errors.media_type" class="mt-1 text-sm text-red-600">
                                {{ saveForm.errors.media_type }}
                            </p>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center justify-end gap-x-6">
                        <FormButton :disabled="saveForm.processing" dusk="save-new-movie-btn">Add Movie</FormButton>
                    </div>
                </form>
            </div>
        </div>
    </Layout>
</template>
