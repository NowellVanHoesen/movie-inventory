<script setup>
defineProps({
    title: {
        type: String,
        default: "",
    },
    release_year: {
        type: String,
        default: "",
    },
    certification: {
        type: String,
        default: "",
    },
});
</script>

<template>
    <div v-bind="$attrs" class="absolute right-0 bottom-0 left-0 rounded-b-xl bg-white/75 p-2 leading-none text-gray-900">
        <p class="font-bold" v-html="title"></p>
        <p v-if="release_year !== ''" class="text-sm">{{ release_year }} {{ certification }}</p>
    </div>
</template>
