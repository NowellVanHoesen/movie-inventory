<script setup>
import Layout from "@/Layouts/Layout.vue";
import MovieCollectionPoster from "../../Components/MovieCollectionPoster.vue";
import BackToTop from "../../Components/BackToTop.vue";
import { InfiniteScroll } from "@inertiajs/vue3";

defineProps({
    collections: {
        type: Object,
        required: true,
    },
    page_title: {
        type: String,
        default: "Movie Collections",
    },
});
</script>
<template>
    <Layout :heading="page_title">
        <InfiniteScroll
            class="mt-6 grid grid-cols-[repeat(auto-fill,minmax(187px,1fr))] place-items-center gap-4"
            data="collections"
            :buffer="50"
        >
            <MovieCollectionPoster v-for="collection in collections.data" :collection="collection" :key="collection.slug" />
        </InfiniteScroll>
        <BackToTop />
    </Layout>
</template>
