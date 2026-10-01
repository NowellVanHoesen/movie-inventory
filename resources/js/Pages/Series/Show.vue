<script setup>
import Layout from "@/Layouts/Layout.vue";
import CastMembers from "@/Components/CastMembers.vue";
import { router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import SeriesInfo from "@/Components/SeriesInfo.vue";
import SeasonList from "@/Components/SeasonList.vue";
import SeasonInfo from "@/Components/SeasonInfo.vue";
import EpisodeList from "@/Components/EpisodeList.vue";
import EpisodeInfo from "@/Components/EpisodeInfo.vue";

const props = defineProps({
    series: {
        type: Object,
        required: true,
    },
    page_title: {
        type: String,
        default: "Movie Inventory - Series Detail",
    },
    // Optional prop: only present after selectEpisode() requests it.
    episode_cast: {
        type: Object,
        default: null,
    },
});

const selectedSeasonId = ref(null);

const selectedEpisodeId = ref(null);

const castList = computed(() => {
    let initialCastList = [...(props.series.cast_members || [])];

    let activeSeason = null;

    if (selectedSeasonId.value) {
        activeSeason = props.series.seasons.find((s) => s.id === selectedSeasonId.value);
        if (activeSeason?.cast_members) {
            initialCastList.push(...activeSeason.cast_members);
        }
    }

    // Ignore a response that belongs to a previously selected episode.
    if (selectedEpisodeId.value && props.episode_cast?.episode_id === selectedEpisodeId.value) {
        initialCastList.push(...props.episode_cast.cast_members);
    }

    const seenIds = new Set();

    return initialCastList.filter((cast_member) => {
        if (!cast_member || !cast_member.id) return false;

        if (seenIds.has(cast_member.id)) {
            return false;
        }

        seenIds.add(cast_member.id);

        return true;
    });
});

const selectedSeason = computed(() => {
    if (!selectedSeasonId.value) return null;
    return props.series.seasons.find((season) => season.id === selectedSeasonId.value);
});

const selectedEpisode = computed(() => {
    if (!selectedSeasonId.value || !selectedEpisodeId.value) return null;

    return selectedSeason.value.episodes.find((episode) => episode.id === selectedEpisodeId.value);
});

const handleSelectedSeason = (seasonId) => {
    selectedSeasonId.value = seasonId;
    selectedEpisodeId.value = null;
};

const handleSelectedEpisode = (episodeId) => {
    selectedEpisodeId.value = episodeId;

    // Episode cast isn't part of the initial payload (see SeriesController@show);
    // preserveUrl keeps ?episode= out of the address bar.
    router.reload({
        only: ["episode_cast"],
        data: { episode: episodeId },
        preserveUrl: true,
    });
};

const handleCloseSeason = () => {
    selectedSeasonId.value = null;
    selectedEpisodeId.value = null;
};

const handleCloseEpisode = () => {
    selectedEpisodeId.value = null;
};
</script>

<template>
    <Layout :heading="page_title" :backdrop="series.backdrop_path">
        <div class="text-cold-steel-900 mb-6 grid gap-4 md:grid-cols-[1fr_350px]">
            <div class="mb-auto grid grid-cols-1 gap-4 lg:mt-6">
                <SeriesInfo :series="series" />
                <div class="grid gap-4">
                    <SeasonList v-if="!selectedSeasonId" :seasons="series.seasons" @selectSeason="handleSelectedSeason" />
                    <SeasonInfo v-if="selectedSeasonId" :season="selectedSeason" @closeSeason="handleCloseSeason" />
                </div>
                <div class="grid gap-4">
                    <EpisodeList
                        v-if="selectedSeasonId && !selectedEpisodeId"
                        :episodes="selectedSeason.episodes"
                        @selectEpisode="handleSelectedEpisode"
                    />
                    <EpisodeInfo
                        v-if="selectedSeasonId && selectedEpisodeId"
                        :episode="selectedEpisode"
                        @closeEpisode="handleCloseEpisode"
                    />
                </div>
            </div>
            <div class="mb-auto rounded-xl bg-white/80 p-6 lg:mt-6">
                <CastMembers :cast="castList" :display_limit="20" :multi_cols="false" />
            </div>
        </div>
    </Layout>
</template>
