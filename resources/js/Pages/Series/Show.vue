<script setup>
import Layout from "../../layouts/Layout.vue";
import ItemPoster from "../Components/ItemPoster.vue";
import CastMembers from "../Components/CastMembers.vue";
import { computed, ref } from "vue";

const props = defineProps({
    series: {
        type: Object,
        required: true,
    },
    page_title: {
        type: String,
        default: "Movie Inventory - Series Detail",
    },
});

const selectedSeasonId = ref(null);

const selectedEpisodeId = ref(null);

const genres = computed(() => {
    if (props.series === false || !props.series?.genres) {
        return "";
    }

    return props.series?.genres?.map((genre) => genre.name).join(" | ");
});

const castList = computed(() => {
    let initialCastList = [...(props.series.cast_members || [])];

    let activeSeason = null;

    if (selectedSeasonId.value) {
        activeSeason = props.series.seasons.find((s) => s.id === selectedSeasonId.value);
        if (activeSeason?.cast_members) {
            initialCastList.push(...activeSeason.cast_members);
        }
    }

    if (selectedEpisodeId.value && activeSeason) {
        const activeEpisode = activeSeason.episodes.find((e) => e.id === selectedEpisodeId.value);
        console.info(activeEpisode);
        if (activeEpisode?.cast_members) {
            initialCastList.push(...activeEpisode.cast_members);
        }
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

const selectSeason = (seasonId) => {
    selectedSeasonId.value = seasonId;
    selectedEpisodeId.value = null;
};

const selectEpisode = (episodeId) => {
    selectedEpisodeId.value = episodeId;
};

const backToSeasonList = () => {
    selectedSeasonId.value = null;
    selectedEpisodeId.value = null;
};

const backToEpisodeList = () => {
    selectedEpisodeId.value = null;
};
</script>

<template>
    <Layout :heading="page_title" :backdrop="series.backdrop_path">
        <div class="text-cold-steel-900 grid grid-cols-1 gap-4 md:grid-cols-3">
            <div
                class="col-span-2 mt-6 mb-auto grid grid-cols-1 gap-4 overflow-hidden rounded-xl bg-white/80 md:grid-cols-[185px_1fr]"
            >
                <ItemPoster :placeholder="series.poster_path === null" :poster_path="series.poster_path" size="w185" class="max-w-[185px]" />
                <div class="m-4 sm:flex sm:flex-col md:ml-0">
                    <p class="text-2xl leading-none">
                        {{ series.name }}
                        <span class="text-sm">({{ series.certification.name }})</span>
                    </p>
                    <p>
                        <em class="text-base">{{ series.tagline }}</em>
                    </p>
                    <p class="text-sm" v-if="series.original_name && series.name !== series.original_name">
                        {{ series.original_name }}
                    </p>
                    <p class="mb-2 text-sm">{{ genres }}</p>
                    <p v-if="!series.purchase_date" class="text-sm font-normal">wishlist</p>
                    <p v-for="(mTypes, parent) in series.media_types_display" :key="parent" class="mt-2 text-sm">
                        <strong>{{ parent }}</strong
                        >:
                        {{ mTypes.map((type) => type).join(" | ") }}
                    </p>
                    <p class="text-sm"><strong>First Air Date</strong>: {{ series.first_air_date }}</p>
                    <p class="mt-4">{{ series.overview }}</p>
                </div>
            </div>
            <div class="row-span-2 mb-auto rounded-xl bg-white/80 p-6 lg:mt-6">
                <CastMembers :cast="castList" :display_limit="20" :multi_cols="false" />
            </div>
            <p v-if="!selectedSeasonId" class="col-span-2 rounded-xl bg-white/80 p-2 text-2xl font-bold">Seasons</p>
            <div class="col-span-2 flex flex-none flex-wrap gap-4">
                <template v-for="season in series.seasons" :key="season.id">
                    <button @click="selectSeason(season.id)" v-if="!selectedSeasonId" class="group relative rounded-lg max-w-[94px]">
                        <ItemPoster
                            :placeholder="season.poster_path === null"
                            :poster_path="season.poster_path"
                            size="w92"
                            class="rounded-lg"
                        />
                        <div
                            class="invisible absolute right-0 bottom-0 left-0 rounded-b-lg bg-white/75 p-2 leading-none group-hover:visible"
                        >
                            <p>{{ season.name }}</p>
                            <p>Ep: {{ season.episodes.length }}</p>
                            <p v-if="season.air_date">{{ season.air_date.substring(0, 4) }}</p>
                        </div>
                    </button>
                    <div
                        v-if="selectedSeasonId === season.id"
                        class="mt-2 mb-auto grid w-full max-w-full gap-2 overflow-hidden rounded-lg bg-white/80 md:grid-cols-[154px_minmax(106px,1fr)]"
                    >
                        <ItemPoster :placeholder="season.poster_path === null" :poster_path="season.poster_path" />
                        <div class="p-2">
                            <button
                                class="text-cold-steel-600 hover:bg-cold-steel-600 focus:bg-cold-steel-600 float-end cursor-pointer rounded-md border border-transparent px-2 py-1 font-semibold transition duration-150 ease-in-out hover:text-white focus:text-white focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 focus:outline-none"
                                @click="backToSeasonList"
                            >
                                <i class="fa-solid fa-xmark fa-lg"></i>
                            </button>
                            <p class="text-xl">{{ season.name }}</p>
                            <p class="text-sm">
                                <strong>First Air Date</strong>:
                                {{ season.air_date }}
                            </p>
                            <p class="text-sm">
                                <strong>Episodes</strong>:
                                {{ season.episodes.length }}
                            </p>
                            <p class="mt-4">{{ season.overview }}</p>
                        </div>
                    </div>
                </template>
            </div>
            <div class="col-span-full">
				<p class="text-2xl font-bold mb-2" v-if="selectedSeasonId && !selectedEpisodeId">Episodes</p>
				<template v-for="season in series.seasons" v-if="selectedSeasonId">
					<div
						class="grid grid-cols-[repeat(auto-fill,minmax(154px,1fr))] gap-2 md:grid-cols-[repeat(auto-fill,minmax(185px,1fr))] lg:col-span-3"
					>
						<template v-for="episode in season.episodes" :key="`${season.id}-${episode.id}`">
							<button
								@click="selectEpisode(episode.id)"
								v-if="selectedSeasonId === season.id && !selectedEpisodeId"
								class="mx-auto grid max-w-47 justify-items-center gap-2 overflow-hidden rounded-lg border bg-white/80"
							>
								<img
									:src="
										episode.still_path
											? 'https://image.tmdb.org/t/p/w185' + episode.still_path
											: $page.props.placeholderStill
									"
									alt=""
								/>
								<span>{{ episode.name }}</span>
							</button>
							<div
								v-if="selectedEpisodeId === episode.id"
								class="mt-2 mb-auto grid gap-2 overflow-hidden rounded-lg bg-white/80 md:col-span-4 md:grid-cols-[300px_1fr]"
							>
								<img
									:src="
										episode.still_path
											? 'https://image.tmdb.org/t/p/w300' + episode.still_path
											: $page.props.placeholderStill
									"
									:alt="`${episode.name} screenshot`"
								/>
								<div class="p-2">
									<button
										class="text-cold-steel-600 hover:bg-cold-steel-600 focus:bg-cold-steel-600 float-end cursor-pointer rounded-md border border-transparent px-2 py-1 font-semibold transition duration-150 ease-in-out hover:text-white focus:text-white focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 focus:outline-none"
										@click="backToEpisodeList"
									>
										<i class="fa-solid fa-xmark fa-lg"></i>
									</button>
									<p class="text-lg">{{ episode.name }}</p>
									<p class="mt-2 text-sm"><strong>First Air Date</strong>: {{ episode.air_date }}</p>
									<p class="text-sm"><strong>Runtime</strong>: {{ episode.runtime }} min.</p>
								</div>
								<p class="mt-4 px-3 pb-2 md:col-span-2">{{ episode.overview }}</p>
							</div>
						</template>
					</div>
				</template>
			</div>
        </div>
    </Layout>
</template>
