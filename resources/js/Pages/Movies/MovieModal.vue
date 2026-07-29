<script setup>
import Modal from "../Components/Modal.vue";
import { Link, useForm } from "@inertiajs/vue3";
import CastMembers from "../Components/CastMembers.vue";
import FormButton from "../Components/FormButton.vue";
import { computed, ref } from "vue";

const props = defineProps({
    movie: Object,
    media_type_options: Object,
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

const isEditing = ref(false);

const form = useForm({
    purchase_date: props.movie.purchase_date,
    media_type: props.movie.media_types?.map((type) => type.id) ?? [],
});

const startEditing = () => {
    form.reset();
    form.clearErrors();
    isEditing.value = true;
};

const cancelEditing = () => {
    form.reset();
    form.clearErrors();
    isEditing.value = false;
};

const submit = () => {
    form.patch(route("movies.update", props.movie.slug), {
        preserveScroll: true,
        except: ["movies"],
        onSuccess: () => {
            isEditing.value = false;
        },
    });
};
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
                    <div class="flex flex-col items-center gap-6" v-if="$page.props.auth.user && !isEditing">
                        <button
                            type="button"
                            dusk="edit-movie-btn"
                            class="bg-cold-steel-600 hover:bg-cold-steel-700 focus:bg-cold-steel-700 inline-flex cursor-pointer items-center rounded-md border border-transparent px-4 py-2 text-sm font-semibold text-white transition duration-150 ease-in-out focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 focus:outline-none"
                            @click="startEditing"
                        >
                            Edit
                        </button>
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
                        <template v-if="!isEditing">
                            <p v-if="movie.purchase_date === null" class="text-cold-steel-600 text-sm font-normal">wishlist</p>
                            <p v-for="(mTypes, parent) in movie.media_types_display" :key="parent" class="mt-2 text-sm">
                                <strong> {{ parent }} </strong>:
                                {{ mTypes.map((type) => type).join(" | ") }}
                            </p>
                        </template>
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
                    <CastMembers v-if="!isEditing" :cast="movie?.cast_members" :display_limit="20" :multi_cols="true" />
                    <form v-else @submit.prevent="submit">
                        <div>
                            <label for="purchase_date" class="block text-sm font-bold">Date Purchased</label>
                            <input
                                type="date"
                                id="purchase_date"
                                dusk="purchase-date-input"
                                v-model="form.purchase_date"
                                class="border-cold-steel-300 mt-1 rounded-md border px-2 py-1 text-sm"
                            />
                            <p v-if="form.errors.purchase_date" class="mt-1 text-sm text-red-600">{{ form.errors.purchase_date }}</p>
                        </div>
                        <div class="mt-4">
                            <span class="block text-sm font-bold">Media Type</span>
                            <div class="mt-1 grid grid-cols-2 gap-2 sm:grid-cols-4">
                                <div v-for="(subTypes, parent) in media_type_options" :key="parent">
                                    <span class="text-sm font-bold">{{ parent }}</span>
                                    <ul>
                                        <li v-for="(name, id) in subTypes" :key="id">
                                            <label class="flex items-center gap-1 text-sm">
                                                <input type="checkbox" :value="Number(id)" :dusk="`media-type-${id}`" v-model="form.media_type" />
                                                {{ name }}
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <p v-if="form.errors.media_type" class="mt-1 text-sm text-red-600">{{ form.errors.media_type }}</p>
                        </div>
                        <div class="mt-4 flex items-center gap-4">
                            <FormButton :disabled="form.processing" dusk="save-movie-btn">Save</FormButton>
                            <button
                                type="button"
                                dusk="cancel-edit-btn"
                                class="text-cold-steel-600 text-sm font-semibold hover:underline"
                                @click="cancelEditing"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Modal>
</template>
