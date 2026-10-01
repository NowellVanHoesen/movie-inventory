<script setup>
import ItemPoster from "./ItemPoster.vue";
import { computed } from "vue";

const props = defineProps({
    series: {
        type: Object,
        required: true,
    },
});

const genres = computed(() => {
    if (props.series === false || !props.series?.genres) {
        return "";
    }

    return props.series?.genres?.map((genre) => genre.name).join(" | ");
});
</script>

<template>
    <div dusk="series-info" class="grid grid-cols-[185px_1fr] overflow-hidden rounded-xl bg-white/80">
        <ItemPoster
            :placeholder="series.poster_path === null"
            :poster_path="series.poster_path"
            size="w185"
            class="max-w-[185px]"
        />
        <div class="m-4 sm:flex sm:flex-col">
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
            <p v-for="(mTypes, parent) in series.media_types_display" :key="parent" class="mt-2 text-sm">
                <strong>{{ parent }}</strong
                >:
                {{ mTypes.map((type) => type).join(" | ") }}
            </p>
            <p class="text-sm"><strong>First Air Date</strong>: {{ series.first_air_date }}</p>
            <p class="mt-4">{{ series.overview }}</p>
        </div>
    </div>
</template>
