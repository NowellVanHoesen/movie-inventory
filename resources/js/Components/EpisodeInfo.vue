<script setup>
defineProps({
    episode: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["closeEpisode"]);
</script>

<template>
    <div
        dusk="episode-info"
        class="mt-2 mb-auto grid grid-cols-[1fr_1fr] gap-2 overflow-hidden rounded-lg bg-white/80 lg:grid-cols-[300px_1fr]"
    >
        <img
            :src="episode.still_path ? 'https://image.tmdb.org/t/p/w300' + episode.still_path : $page.props.placeholderStill"
            :alt="`${episode.name} screenshot`"
        />
        <div class="p-2">
            <button
                class="text-cold-steel-600 hover:bg-cold-steel-600 focus:bg-cold-steel-600 float-end cursor-pointer rounded-md border border-transparent px-2 py-1 font-semibold transition duration-150 ease-in-out hover:text-white focus:text-white focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 focus:outline-none"
                dusk="episode-close-btn"
                @click="$emit('closeEpisode')"
            >
                <i class="fa-solid fa-xmark fa-lg"></i>
            </button>
            <p class="text-lg">{{ episode.name }}</p>
            <p class="mt-2 text-sm"><strong>First Air Date</strong>: {{ episode.air_date }}</p>
            <p class="text-sm"><strong>Runtime</strong>: {{ episode.runtime }} min.</p>
        </div>
        <p class="mt-2 px-3 pb-2 col-span-2">{{ episode.overview }}</p>
    </div>
</template>
