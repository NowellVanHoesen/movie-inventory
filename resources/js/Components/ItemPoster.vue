<script setup>
import { computed } from "vue";

const props = defineProps({
    size: {
        type: String,
        default: "w185",
    },
    poster_path: {
        type: String,
        default: "",
    },
    placeholder: {
        type: Boolean,
        default: false,
    },
});

const max_w = computed(() => {
    switch (props.size) {
        case "w92":
            return "94px";
        case "w154":
            return "156px";
        case "w185":
            return "187px";
        default:
            return "300px";
    }
});
</script>

<template>
    <img
        :src="placeholder ? $page.props.placeholderPoster : 'https://image.tmdb.org/t/p/' + size + poster_path"
        :class="`border-cold-steel-900 w-full border max-w-[${max_w}]`"
    />
</template>
