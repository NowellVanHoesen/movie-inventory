<script setup>
import Modal from "../Components/Modal.vue";
import { Link } from "@inertiajs/vue3";
import CastMembers from "../Components/CastMembers.vue";
import { computed } from "vue";

const props = defineProps({
    movie: Object,
});

const emit = defineEmits(["close"]);

const closeModal = () => {
    emit("close");
};

const genres = computed(() => {
    if (props.movie === false || !props.movie?.genres) {
        return "";
    }

    return props.movie?.genres?.map((genre) => genre.name).join(" | ");
});
</script>

<template>
    <Modal @close="closeModal">
        <div class="relative gap-4 rounded-xl bg-white/80 p-6 text-gray-900 md:grid md:grid-cols-[185px_1fr]">
            <div class="grid h-fit w-fit grid-flow-col gap-4 md:w-auto md:grid-flow-row">
                <img :src="`https://image.tmdb.org/t/p/w185/${movie.poster_path}`" :alt="`${movie.title} movie poster`" />
                <div>
                    <p class="mb-2 text-center">
                        <strong>Release Date</strong><br />
                        {{ movie.release_date }}
                    </p>
                    <p class="mb-6 text-center text-sm font-normal">
                        ( {{ movie.certification.name }} ) {{ movie.runtime }} min.
                    </p>
                    <div class="flex flex-col items-center gap-6" v-if="$page.props.auth.user">
                        <Link
                            :href="movie.edit_link"
                            class="bg-cold-steel-600 hover:bg-cold-steel-700 focus:bg-cold-steel-700 inline-flex items-center rounded-md border border-transparent px-4 py-2 text-sm font-semibold text-white transition duration-150 ease-in-out focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 focus:outline-none"
                        >
                            Edit
                        </Link>
                        <Link
                            href=""
                            method="delete"
                            as="button"
                            class="inline-flex cursor-pointer items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-sm font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out hover:bg-red-500 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:outline-none active:bg-red-700"
                        >
                            Delete
                        </Link>
                        <form method="POST" :action="movie.delete_link" id="delete-movie" class="hidden"></form>
                    </div>
                </div>
            </div>
            <div>
                <div class="sm:flex sm:items-start sm:justify-between">
                    <div>
                        <h2 class="text-2xl">
                            {{ movie.title }}
                        </h2>
                        <p>
                            <em>{{ movie.tagline }}</em>
                        </p>
                        <p v-if="genres" class="mt-2 text-sm">{{ genres }}</p>
                        <p v-if="movie.purchase_date === null" class="text-cold-steel-600 text-sm font-normal">wishlist</p>
                        <p v-for="(mTypes, parent) in movie.media_types_display" :key="parent" class="mt-2 text-sm">
                            <strong> {{ parent }} </strong>:
                            {{ mTypes.map((type) => type).join(" | ") }}
                        </p>
                        <p v-if="movie.collection" class="mt-2">
                            <Link
                                :href="route('movieCollection.show', movie.collection)"
                                class="text-blue-600 hover:underline focus:underline"
                            >
                                {{ movie.collection.name }}
                            </Link>
                        </p>
                    </div>
                    <button
                        class="text-cold-steel-600 hover:bg-cold-steel-600 focus:bg-cold-steel-600 absolute top-2 right-2 cursor-pointer rounded-md border border-transparent px-2 py-1 font-semibold transition duration-150 ease-in-out hover:text-white focus:text-white focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 focus:outline-none md:relative md:top-0 md:right-0"
                        @click="closeModal"
                    >
                        <i class="fa-solid fa-xmark fa-lg"></i>
                    </button>
                </div>
                <p class="mt-4">{{ movie.overview }}</p>
                <div class="mt-4">
                    <CastMembers :cast="movie?.cast_members" :display_limit="20" :multi_cols="true" />
                </div>
            </div>
        </div>
    </Modal>
</template>
