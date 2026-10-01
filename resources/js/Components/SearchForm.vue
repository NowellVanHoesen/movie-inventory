<script setup>
import { useForm } from "@inertiajs/vue3";
import FormButton from "./FormButton.vue";

const form = useForm({
    search: "",
});
</script>

<template>
    <form @submit.prevent="form.get(route('search'))" class="w-full flex-1 md:flex md:justify-end">
        <div
            class="m-4 md:my-auto grid md:w-full max-w-full min-w-45 grid-cols-1 rounded-md shadow-sm ring-1 ring-gray-300 ring-inset focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-inset md:max-w-80"
        >
            <input
                name="search"
                type="search"
                v-model="form.search"
                placeholder="Search the site..."
                aria-label="Search"
                dusk="header-search-input"
            />
            <i
                class="fa-solid fa-magnifying-glass text-cold-steel-200 pointer-events-none col-start-1 row-start-1 ml-3 block h-5 w-5 self-center align-middle leading-6"
            ></i>
            <FormButton class="hidden" dusk="header-search-submit-btn"></FormButton>
        </div>
    </form>
</template>
