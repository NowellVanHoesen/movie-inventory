<script setup>
import Layout from "@/Layouts/Layout.vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import ItemPoster from "../Components/ItemPoster.vue";
import ItemOverlayDetail from "../Components/ItemOverlayDetail.vue";
import ItemStatusIcon from "../Components/ItemStatusIcon.vue";
import FormButton from "../Components/FormButton.vue";
import { computed } from "vue";

const props = defineProps({
    page_title: {
        type: String,
        default: "Add Series",
    },
    search_term: {
        type: String,
        default: "",
    },
    search_results: {
        type: Object,
        default: null,
    },
    local_results: {
        type: Array,
        default: () => [],
    },
    series_detail: {
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
});

const submitSearch = () => {
    searchForm.get(route("series.create"), {
        preserveState: true,
    });
};

const localSeriesFor = (tmdbId) => props.local_results.find((localSeries) => localSeries.id === tmdbId);

const releaseYear = (firstAirDate) => (firstAirDate ? firstAirDate.slice(0, 4) : "TBA");

// Not yet in the local database — send the user back through this same page
// (GET) with `series_id` set, so the controller fetches and shows TMDB detail.
const selectSeries = (tmdbId) => {
    router.get(route("series.create"), {
        series_id: tmdbId,
        search_term: props.search_term,
    });
};

const contentRating = computed(() => {
    const usRating = props.series_detail?.content_ratings?.results?.find((result) => result.iso_3166_1 === "US");

    return usRating?.rating ?? "";
});

const genres = computed(() => (props.series_detail?.genres ?? []).join(" | "));

const saveForm = useForm({
    series_id: props.series_detail?.id ?? null,
    purchase_date: "",
    media_type: [],
    season_numbers: [],
});

const submitSave = () => {
    saveForm.post(route("series.store"));
};
</script>

<template>
    <Layout :heading="page_title" :backdrop="series_detail?.backdrop_path">
        <form class="mx-auto max-w-xl" @submit.prevent="submitSearch">
            <div class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-4">
                <div class="sm:col-span-3">
                    <label for="query" class="block text-sm font-bold">Title Search</label>
                    <input
                        id="query"
                        type="text"
                        v-model="searchForm.query"
                        minlength="2"
                        required
                        autofocus
                        dusk="series-search-query-input"
                        class="border-cold-steel-300 mt-1 w-full rounded-md border px-2 py-1 text-sm text-gray-900"
                    />
                    <p v-if="searchForm.errors.query" class="mt-1 text-sm text-red-600">{{ searchForm.errors.query }}</p>
                </div>
                <div class="flex items-end">
                    <FormButton :disabled="searchForm.processing" dusk="series-search-submit-btn">Search</FormButton>
                </div>
            </div>
        </form>

        <div v-if="search_results" class="mt-6 grid grid-cols-[repeat(auto-fill,minmax(185px,1fr))] gap-5">
            <template v-for="result in search_results.results" :key="result.id">
                <Link
                    v-if="localSeriesFor(result.id)"
                    :href="route('series.show', localSeriesFor(result.id).slug)"
                    class="group relative mb-auto block max-w-fit overflow-hidden rounded-xl"
                    :dusk="`series-search-result-${result.id}`"
                >
                    <ItemStatusIcon :purchased="localSeriesFor(result.id).purchase_date !== null" />
                    <ItemPoster :placeholder="!result.poster_path" :poster_path="result.poster_path" />
                    <ItemOverlayDetail
                        class="text-left invisible group-hover:visible group-focus:visible"
                        :title="result.name"
                        :release_year="releaseYear(result.first_air_date)"
                    />
                </Link>
                <button
                    v-else
                    type="button"
                    class="group relative mb-auto block max-w-fit overflow-hidden rounded-xl"
                    :dusk="`series-search-result-${result.id}`"
                    @click="selectSeries(result.id)"
                >
                    <ItemPoster :placeholder="!result.poster_path" :poster_path="result.poster_path" />
                    <ItemOverlayDetail
                        :class="result.poster_path ? 'text-left invisible group-hover:visible group-focus:visible' : 'text-left'"
                        :title="result.name"
                        :release_year="releaseYear(result.first_air_date)"
                    />
                </button>
            </template>
            <p v-if="search_results.results.length === 0" class="text-cold-steel-100">No results found.</p>
        </div>

        <div v-else-if="series_detail" class="mt-6 grid gap-4 lg:grid-cols-[1fr_minmax(280px,40%)]">
            <div class="grid gap-4 rounded-xl bg-white/80 p-6 text-gray-900 md:grid-cols-[185px_1fr]">
                <img :src="`https://image.tmdb.org/t/p/w185${series_detail.poster_path}`" :alt="`${series_detail.name} series poster`" />
                <div class="sm:flex sm:flex-col">
                    <p class="text-2xl leading-none">
                        {{ series_detail.name }}
                        <span class="text-sm">( {{ contentRating }} )</span>
                    </p>
                    <p v-if="series_detail.tagline">
                        <em class="text-base">{{ series_detail.tagline }}</em>
                    </p>
                    <p v-if="series_detail.original_name && series_detail.original_name !== series_detail.name" class="text-sm">
                        {{ series_detail.original_name }}
                    </p>
                    <p class="text-sm">{{ genres }}</p>
                    <p class="text-sm"><strong>First Air Date</strong>: {{ series_detail.first_air_date }}</p>
                    <p class="mt-4">{{ series_detail.overview }}</p>
                </div>
            </div>
            <div class="rounded-xl bg-white/70 p-4 text-gray-900">
                <form @submit.prevent="submitSave">
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
                    <div class="mt-4">
                        <span class="block text-sm font-bold">Media Type</span>
                        <div class="grid grid-cols-2">
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
                    <div class="mt-6 text-right">
                        <FormButton :disabled="saveForm.processing" dusk="save-new-series-btn">Add Series</FormButton>
                    </div>
                </form>
            </div>
            <div class="lg:col-span-full">
                <p class="mb-2 text-xl text-cold-steel-100">Seasons</p>
                <div class="grid grid-cols-[repeat(auto-fill,minmax(225px,1fr))] gap-4">
                    <label
                        v-for="season in series_detail.seasons"
                        :key="season.season_number"
                        class="grid grid-cols-[94px_minmax(106px,1fr)] gap-2 rounded-lg border bg-white/70 text-gray-900"
                        :class="saveForm.season_numbers.includes(season.season_number) ? 'shadow-lg shadow-blue-500' : ''"
                    >
                        <ItemPoster :placeholder="!season.poster_path" :poster_path="season.poster_path" size="w92" />
                        <div class="p-2">
                            <p>
                                {{ series_detail.name }}: {{ season.name }}
                                <span v-if="season.air_date">( {{ season.air_date.slice(0, 4) }} )</span>
                            </p>
                            <p>Episodes: {{ season.episode_count }}</p>
                            <input
                                type="checkbox"
                                class="hidden"
                                :value="season.season_number"
                                v-model="saveForm.season_numbers"
                                :dusk="`season-${season.season_number}`"
                            />
                        </div>
                    </label>
                </div>
            </div>
        </div>
    </Layout>
</template>
