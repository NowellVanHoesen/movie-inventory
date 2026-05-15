<script setup>
import { computed } from "vue";
import Layout from "@/Layouts/Layout.vue";
import CastMembers from "../Components/CastMembers.vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    page_title: {
        type: String,
        default: "",
    },
    movie: {
        type: Object,
        required: true,
    },
    recommendations: {
        type: Array,
        required: false,
    },
    owned_recommendations: {
        type: Array,
        required: false,
    },
});

const genres = computed(() => {
    return props.movie.genres.map((genre) => genre.name).join(" | ");
});
</script>

<template>
    <Layout :heading="props.page_title">
        <div class="mt-6 gap-4 rounded-xl bg-white/80 p-6 text-gray-900 md:grid md:grid-cols-[185px_1fr]">
            <div>
                <img :src="`https://image.tmdb.org/t/p/w185/${movie.poster_path}`" :alt="`${movie.title} movie poster`" />
            </div>
            <div>
                <div class="sm:flex sm:items-start sm:justify-between">
                    <div>
                        <h2 class="text-2xl">
                            {{ props.movie.title }}
                            <span class="text-sm font-normal"
                                >( {{ props.movie.certification }} ) {{ props.movie.runtime }} min.</span
                            >
                        </h2>
                        <p>
                            <em>{{ props.movie.tagline }}</em>
                        </p>
                        <p class="text-sm">{{ genres }}</p>
                        <p v-if="props.movie.purchase_date === null" class="text-cold-steel-600 text-sm font-normal">
                            wishlist
                        </p>
                        <p
                            v-for="(parent, media_types) in props.movie.media_types_display"
                            :key="parent"
                            class="mt-2 text-sm"
                        >
                            <strong>{{ parent }}</strong
                            >:
                            {{ media_types.map((type) => type).join(" | ") }}
                        </p>
                        <p v-if="props.movie.collection" class="mt-2">
                            <Link
                                :href="route('movieCollection.show', props.movie.collection)"
                                class="text-blue-600 hover:underline focus:underline"
                            >
                                {{ props.movie.collection.name }}
                            </Link>
                        </p>
                    </div>
                    <div class="flex items-center gap-8" v-if="$page.props.auth.user">
                        <div class="place-content-center">
                            <Link
                                :href="route('movies.edit', props.movie)"
                                class="bg-cold-steel-600 hover:bg-cold-steel-700 focus:bg-cold-steel-700 inline-flex items-center rounded-md border border-transparent px-4 py-2 text-sm font-semibold text-white transition duration-150 ease-in-out focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 focus:outline-none"
                            >
                                Edit
                        </Link>
                        </div>
                        <Link
                            :href="route('movies.destroy', props.movie)"
                            method="delete"
                            as="button"
                            class="inline-flex cursor-pointer items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-sm font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out hover:bg-red-500 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:outline-none active:bg-red-700"
                            @click.prevent="
                                if (confirm('Are you sure you want to delete this movie?')) {
                                    $inertia.delete(route('movies.destroy', props.movie));
                                }
                            "
                        >
                            Delete
                        </Link>
                    </div>
                    <form method="POST" :action="route('movies.destroy', movie)" id="delete-movie" class="hidden"></form>
                </div>
                <p class="mt-4">{{ props.movie.overview }}</p>
                <div class="mt-4">
                    <CastMembers :cast="props.movie.cast_members" :display_limit="20" :multi_cols="true" />
                </div>
            </div>
        </div>
    </Layout>
</template>
