<script setup>
import { ref, watch } from "vue";

defineProps({
    genres: {
        type: Array,
        required: false,
        default: () => [],
    },
    sortCol: {
        type: String,
        required: false,
        default: "release_date",
    },
    sortDir: {
        type: String,
        required: false,
        default: "desc",
    },
});

const getCookie = (name) => {
    const match = document.cookie.match(new RegExp("(?:^|; )" + name + "=([^;]*)"));
    return match ? decodeURIComponent(match[1]) : null;
};

// Deliberately a session cookie (no max-age): mirrors the previous
// sessionStorage-based lifetime, but cookies also ride along on a full
// browser refresh so the server can honor them on the initial page load.
const setCookie = (name, value) => {
    document.cookie = `${name}=${encodeURIComponent(value)}; path=/; SameSite=Lax`;
};

const removeCookie = (name) => {
    document.cookie = `${name}=; path=/; max-age=0`;
};

const showPanel = ref(false);
const selectedGenres = ref(JSON.parse(getCookie("selectedGenres")) || []);
const sortCol = ref(getCookie("sortCol") || "release_date");
const sortDir = ref(getCookie("sortDir") || "desc");

watch(selectedGenres, (newVal) => {
    if (newVal.length) {
        setCookie("selectedGenres", JSON.stringify(newVal));
    } else {
        removeCookie("selectedGenres");
    }
});

watch(sortCol, (newCol) => {
    setCookie("sortCol", newCol);
});

watch(sortDir, (newDir) => {
    setCookie("sortDir", newDir);
});

const clearFilters = () => {
    selectedGenres.value = [];
    removeCookie("selectedGenres");
};

const emit = defineEmits(["apply-filters"]);

const applyFilters = () => {
    emit("apply-filters");
    showPanel.value = false;
};
</script>

<template>
    <div>
        <button
            class="absolute top-0 right-2 z-40 cursor-pointer rounded-b-md bg-white/10 px-2.5 py-1.5 text-sm font-semibold text-white inset-ring inset-ring-white/5 hover:bg-white/20"
            @click="showPanel = true"
            v-if="!showPanel"
        >
            <i class="fa-solid fa-sliders"></i>
        </button>
        <Transition
            enter-from-class="transform -translate-y-full"
            enter-active-class="transition ease-out duration-300"
            enter-to-class="transform translate-y-0"
            leave-from-class="transform translate-y-0"
            leave-active-class="transition ease-in duration-200"
            leave-to-class="transform -translate-y-full"
        >
            <div
                v-if="showPanel"
                class="bg-cold-steel-900 absolute top-0 right-0 left-0 z-10 flex flex-col overflow-y-auto rounded-b-lg p-2 lg:p-6"
            >
                <div class="flex justify-between">
                    <h2 class="text-2xl font-semibold text-white">Filter Options</h2>
                    <button @click="showPanel = false" class="cursor-pointer px-2 py-3">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="relative mt-6 flex flex-1 gap-6">
                    <div class="basis-2/3">
                        <h3 class="mb-2 text-xl font-medium text-white">
                            Genres <span class="text-sm">(select multiple)</span>
                        </h3>
                        <div class="flex flex-wrap gap-3">
                            <label
                                v-for="genre in genres"
                                :key="genre.name"
                                :for="genre.name"
                                class="text-cold-steel-300 hover:text-cold-steel-100 has-checked:text-cold-steel-100 inline-block cursor-pointer rounded-md border border-transparent bg-transparent px-2 py-1 text-sm font-medium hover:border-blue-600 has-checked:border-blue-800 has-checked:hover:border-blue-600"
                            >
                                <input
                                    type="checkbox"
                                    :value="genre.name"
                                    :id="genre.name"
                                    v-model="selectedGenres"
                                    class="hidden"
                                />{{ genre.name }}
                            </label>
                        </div>
                        <button
                            @click="clearFilters"
                            class="bg-cold-steel-600 hover:bg-cold-steel-300 hover:text-cold-steel-900 disabled:bg-cold-steel-800 disabled:text-cold-steel-500 mt-8 inline-block cursor-pointer rounded-md border border-transparent px-4 py-3 text-sm font-medium"
                            v-bind:disabled="selectedGenres.length === 0"
                        >
                            Clear Genre Filter
                        </button>
                    </div>
                    <div class="basis-1/3">
                        <h3 class="mb-2 text-xl font-medium text-white">Sort</h3>
                        <h4 class="text mb-1 font-medium text-white">Column</h4>
                        <div class="flex flex-wrap gap-3">
                            <label
                                for="sort_title"
                                class="text-cold-steel-300 hover:text-cold-steel-100 has-checked:text-cold-steel-100 inline-block cursor-pointer rounded-md border border-transparent bg-transparent px-2 py-1 text-sm font-medium hover:border-blue-600 has-checked:border-blue-800 has-checked:hover:border-blue-600"
                            >
                                <input
                                    type="radio"
                                    value="title_sortable"
                                    id="sort_title"
                                    v-model="sortCol"
                                    class="hidden"
                                />Title
                            </label>
                            <label
                                for="sort_release_date"
                                class="text-cold-steel-300 hover:text-cold-steel-100 has-checked:text-cold-steel-100 inline-block cursor-pointer rounded-md border border-transparent bg-transparent px-2 py-1 text-sm font-medium hover:border-blue-600 has-checked:border-blue-800 has-checked:hover:border-blue-600"
                            >
                                <input
                                    type="radio"
                                    value="release_date"
                                    id="sort_release_date"
                                    v-model="sortCol"
                                    class="hidden"
                                />Release Date
                            </label>
                            <label
                                for="sort_purchase_date"
                                class="text-cold-steel-300 hover:text-cold-steel-100 has-checked:text-cold-steel-100 inline-block cursor-pointer rounded-md border border-transparent bg-transparent px-2 py-1 text-sm font-medium hover:border-blue-600 has-checked:border-blue-800 has-checked:hover:border-blue-600"
                            >
                                <input
                                    type="radio"
                                    value="purchase_date"
                                    id="sort_purchase_date"
                                    v-model="sortCol"
                                    class="hidden"
                                />Purchase Date
                            </label>
                        </div>
                        <h4 class="text mt-2 mb-1 font-medium text-white">Direction</h4>
                        <div class="flex flex-wrap gap-3">
                            <label
                                for="sort_ascending"
                                class="text-cold-steel-300 hover:text-cold-steel-100 has-checked:text-cold-steel-100 inline-block cursor-pointer rounded-md border border-transparent bg-transparent px-2 py-1 text-sm font-medium hover:border-blue-600 has-checked:border-blue-800 has-checked:hover:border-blue-600"
                            >
                                <input
                                    type="radio"
                                    value="asc"
                                    id="sort_ascending"
                                    v-model="sortDir"
                                    class="hidden"
                                />Ascending
                            </label>
                            <label
                                for="sort_descending"
                                class="text-cold-steel-300 hover:text-cold-steel-100 has-checked:text-cold-steel-100 inline-block cursor-pointer rounded-md border border-transparent bg-transparent px-2 py-1 text-sm font-medium hover:border-blue-600 has-checked:border-blue-800 has-checked:hover:border-blue-600"
                            >
                                <input
                                    type="radio"
                                    value="desc"
                                    id="sort_descending"
                                    v-model="sortDir"
                                    class="hidden"
                                />Descending
                            </label>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button
                        @click="applyFilters"
                        class="bg-cold-steel-600 hover:bg-cold-steel-700 focus:bg-cold-steel-700 inline-flex items-center rounded-md border border-transparent px-4 py-2 text-sm font-semibold text-white transition duration-150 ease-in-out focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 focus:outline-none"
                    >
                        Apply
                    </button>
                </div>
            </div>
        </Transition>
    </div>
</template>
