<script setup>
const props = defineProps({
    episodes: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(["selectEpisode"]);

const handleSelectedEpisode = (id) => {
    emit("selectEpisode", id);
};
</script>

<template>
    <h3 class="h-auto w-full content-end rounded-xl bg-white/80 p-2 text-2xl font-bold">Episodes</h3>
    <div
        class="grid grid-cols-[repeat(auto-fill,minmax(154px,1fr))] gap-2 md:grid-cols-[repeat(auto-fill,minmax(185px,1fr))]"
    >
        <template v-for="episode in episodes" :key="episode.id">
			<button
				@click="handleSelectedEpisode(episode.id)"
				:dusk="`episode-btn-${episode.id}`"
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
		</template>
    </div>
</template>
