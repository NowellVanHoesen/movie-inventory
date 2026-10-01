<script setup>
import ItemPoster from './ItemPoster.vue';

defineProps({
    season: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["closeSeason"]);
</script>

<template>
    <div
        dusk="season-info"
        class="mt-2 mb-auto grid w-full max-w-full grid-cols-[154px_minmax(106px,1fr)] gap-2 overflow-hidden rounded-lg bg-white/80"
    >
        <ItemPoster :placeholder="season.poster_path === null" :poster_path="season.poster_path" />
        <div class="p-2">
            <button
                class="text-cold-steel-600 hover:bg-cold-steel-600 focus:bg-cold-steel-600 float-end cursor-pointer rounded-md border border-transparent px-2 py-1 font-semibold transition duration-150 ease-in-out hover:text-white focus:text-white focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 focus:outline-none"
                dusk="season-close-btn"
                @click="$emit('closeSeason')"
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
