<script setup>
import FormButton from "./FormButton.vue";
import Nav from "./Nav.vue";
import NavLink from "./NavLink.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import ProfileDropdown from "./ProfileDropdown.vue";

let currentProfile = computed(() => usePage().props.value.auth.user);

const form = useForm({
    search: "",
});
</script>

<template>
    <header class="bg-cold-steel-700 shadow-sm z-50">
        <div class="text-cold-steel-100 relative flex h-16 flex-row place-items-center px-4 py-4 md:justify-normal">
            <!-- TODO: fix menu toggle -->
            <div class="-left-1 flex w-full justify-between px-4 sm:px-6 md:mx-auto md:max-w-7xl md:justify-normal lg:px-8">
                <!-- Logo -->
                <Link :href="route('home')" class="place-self-center">
                    <i class="fa-solid fa-clapperboard fa-2xl" title="Movie Inventory"></i>
                </Link>
                <!-- Mobile menu button -->
                <button
                    class="h-10 w-10 rounded-lg p-2 text-gray-700 hover:bg-gray-100 focus:ring-2 focus:ring-gray-200 focus:outline-none md:hidden dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
                    aria-controls="navbar-dropdown"
                    aria-expanded="false"
                >
                    <span class="sr-only">Open main menu</span>
                    <i class="fa-solid fa-xl"></i>
                </button>
                <div
                    class="absolute top-full right-0 left-0 z-15 rounded-b-lg px-2 drop-shadow-lg md:relative md:flex md:w-full md:flex-row md:items-center md:justify-between md:rounded-none md:shadow-none"
                >
                    <!-- Primary Navigation Menu -->
                    <Nav aria-label="main" class="md:flex md:grow md:space-x-4">
                        <NavLink :href="route('home')" :active="route().current('home')"> Home </NavLink>
                        <NavLink :href="route('movies.index')" :active="route().current('movies.*')">Movies</NavLink>
                        <NavLink :href="route('movieCollection.index')" :active="route().current('movieCollection.*')">
                            Movie Collections
                        </NavLink>
                        <NavLink :href="route('series.index')" :active="route().current('series.*')">Series</NavLink>
                    </Nav>
                    <div class="my-2 flex flex-1 px-2 md:ml-6 md:justify-end">
                        <form @submit.prevent="form.get(route('search'))" class="w-full">
                            <div
                                class="grid w-full max-w-full min-w-45 grid-cols-1 rounded-md shadow-sm ring-1 ring-gray-300 ring-inset focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-inset md:max-w-80"
                            >
                                <input
                                    name="search"
                                    type="search"
                                    v-model="form.search"
                                    placeholder="Search the site..."
                                    aria-label="Search"
                                    class="placeholder:text-cold-steel-200 col-start-1 row-start-1 block w-full rounded-md border-0 bg-gray-50 py-1.5 pr-3 pl-10 text-gray-900 outline-none focus:ring-0 sm:text-sm sm:leading-6"
                                />
                                <i
                                    class="fa-solid fa-magnifying-glass text-cold-steel-200 pointer-events-none col-start-1 row-start-1 ml-3 block h-5 w-5 self-center align-middle leading-6"
                                ></i>
                            </div>
                            <FormButton class="hidden"></FormButton>
                        </form>
                    </div>
                    <Nav aria-label="profile" class="flex grow-0 flex-row justify-end space-x-4">
                        <ProfileDropdown v-if="$page.props.auth.user">
                            <NavLink :href="route('dashboard')" active="Route::is('dashboard')">Dashboard</NavLink>
                            <NavLink :href="route('profile.edit')">Profile</NavLink>
                            <form method="POST" action="route('logout')">
                                <NavLink
                                    :href="route('logout')"
                                    onclick="
                                        event.preventDefault();
                                        this.closest('form').submit();
                                    "
                                >
                                    Log Out
                                </NavLink>
                            </form>
                        </ProfileDropdown>
                        <NavLink v-if="!$page.props.auth.user" :href="route('login')" :active="route().current('login')">
                            Log in
                        </NavLink>

                        <NavLink
                            v-if="route().has('register')"
                            :href="route('register')"
                            :active="route().current('register')"
                        >
                            Register
                        </NavLink>
                    </Nav>
                </div>
            </div>
        </div>
    </header>
</template>
