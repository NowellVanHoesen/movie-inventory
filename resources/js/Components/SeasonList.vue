<script setup>
import ItemPoster from "./ItemPoster.vue";

defineProps({
    seasons: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(["selectSeason"]);

const handleSelectedSeason = (id) => {
    emit("selectSeason", id);
};
</script>

<template>
    <h2 class="h-auto w-full content-end rounded-xl bg-white/80 p-2 text-2xl font-bold">Seasons</h2>
    <div class="grid grid-cols-[repeat(auto-fill,minmax(94px,1fr))] place-items-center gap-2">
        <template v-for="season in seasons" :key="season.id">
            <button
                @click="handleSelectedSeason(season.id)"
                :dusk="`season-btn-${season.id}`"
                class="group relative max-w-[94px] rounded-lg"
            >
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
        </template>
    </div>
</template>
