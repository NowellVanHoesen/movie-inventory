<script setup>
import Layout from "@/Layouts/Layout.vue";
import MoviePoster from "../Components/MoviePoster.vue";
import FilterDropdown from "../Components/FilterDropdown.vue";
import BackToTop from "../Components/BackToTop.vue";
import MovieModal from "./MovieModal.vue";
import { InfiniteScroll, router, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    movies: {
        type: Object,
        required: true,
    },
    page_title: {
        type: String,
        default: "Movies",
    },
    genres: {
        type: Array,
        required: false,
        default: () => [],
    },
});

const page = usePage();
const nextPageUrl = computed(() => props.movies.links.next);
const hasMorePages = computed(() => !!nextPageUrl.value);

const applyFilters = () => {
    router.get(
        page.url,
        {},
        {
            reset: ["movies"],
            only: ["movies"],
        },
    );
};
</script>

<template>
    <Layout :heading="page_title">
        <FilterDropdown
            :genres="genres"
            :sort-options="[
                { value: 'title_sortable', label: 'Title' },
                { value: 'release_date', label: 'Release Date' },
                { value: 'purchase_date', label: 'Purchase Date' },
            ]"
            default-sort-col="release_date"
            default-sort-dir="desc"
            :cookie-names="{ genres: 'selectedGenres', sortCol: 'sortCol', sortDir: 'sortDir' }"
            @apply-filters="applyFilters"
        />
        <InfiniteScroll
            class="mt-6 grid grid-cols-[repeat(auto-fill,minmax(187px,1fr))] place-items-center gap-4"
            data="movies"
            :buffer="300"
        >
            <MoviePoster v-for="movie in movies.data" :href="route('movies.show', movie)" :movie="movie" :key="movie.slug" />
        </InfiniteScroll>
        <BackToTop />
    </Layout>
</template>
