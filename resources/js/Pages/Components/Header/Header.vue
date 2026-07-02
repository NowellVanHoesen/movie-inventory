<script setup>
import { computed, ref } from "vue";
import { Link, usePage, useForm } from "@inertiajs/vue3";
import ProfileDropdown from "./ProfileDropdown.vue";
import FormButton from "../FormButton.vue";

let isLoggedIn = computed(() => usePage().props.auth.user);

const navLinks = [
    { name: "Home", hrefRoute: "home", authRequired: false },
    {
        name: "Movies",
        activeCondition: ["movies.*", "movieCollection.*"],
        children: [
            {
                hrefRoute: "movies.index",
                name: "All",
                authRequired: false,
            },
            {
                hrefRoute: "movies.purchased",
                name: "Purchased",
                authRequired: false,
            },
            {
                hrefRoute: "movies.wishlist",
                name: "Wishlist",
                authRequired: false,
            },
            {
                hrefRoute: "movieCollection.index",
                name: "Collections",
                authRequired: false,
            },
            {
                hrefRoute: "series.create",
                name: "Add Movie",
                authRequired: true,
            },
        ],
    },
    {
        name: "Series",
        activeCondition: ["series.*"],
        children: [
            {
                hrefRoute: "series.index",
                name: "All",
                authRequired: false,
            },
            {
                hrefRoute: "series.create",
                name: "Add Series",
                authRequired: true,
            },
        ],
    },
];

const isMobileMenuOpen = ref(false);
const inProfileMenuOpen = ref(false);
const activeDropdown = ref(null);
const activeMobileMenu = ref(null);
const form = useForm({
    search: "",
});

const toggleDropdown = (menuName) => {
    activeDropdown.value = activeDropdown.value === menuName ? null : menuName;
};

const toggleMobileSubMenu = (menuName) => {
    activeMobileMenu.value = activeMobileMenu.value === menuName ? null : menuName;
};
</script>

<template>
    <header class="bg-cold-steel-700 z-50 shadow-sm">
        <div class="text-cold-steel-100 relative flex h-16 flex-row place-items-center px-4 py-4 md:justify-normal">
            <div class="relative -left-1 flex w-full justify-between px-4 md:mx-auto md:max-w-7xl md:justify-normal">
                <div
                    class="relative top-full right-0 left-0 z-15 flex w-full flex-row items-center justify-between gap-4 drop-shadow-lg"
                >
                    <Link :href="route('home')" class="">
                        <i class="fa-solid fa-clapperboard fa-2xl" title="Movie Inventory"></i>
                    </Link>
                    <nav class="hidden items-center md:flex md:grow md:gap-4" aria-label="main">
                        <template v-for="item in navLinks" :key="item.name">
                            <Link
                                v-if="!item.children"
                                :href="route(item.hrefRoute)"
                                class="text-cold-steel-100 hover:bg-cold-steel-900 hover:text-cold-steel-50 focus:db-cold-steel-900 block rounded-md px-4 py-3 text-sm font-medium focus:inset-ring-blue-300 md:inline-block md:px-3 md:py-2"
                                :class="{ 'bg-cold-steel-900': route().current(item.hrefRoute) }"
                                >Home</Link
                            >
                            <div v-else class="relative">
                                <button
                                    @click="toggleDropdown(item.name)"
                                    class="text-cold-steel-100 hover:bg-cold-steel-900 hover:text-cold-steel-50 focus:bg-cold-steel-900 block rounded-md px-4 py-3 text-sm font-medium whitespace-nowrap focus:inset-ring-blue-300 md:inline-block md:px-3 md:py-2"
                                    :class="{
                                        'bg-cold-steel-900':
                                            activeDropdown === item.name ||
                                            item.activeCondition.some((r) => route().current(r)),
                                    }"
                                    :aria-current="activeDropdown === item.name ? 'page' : 'false'"
                                >
                                    {{ item.name }}
                                    <i
                                        class="fa-solid fa-chevron-down transform transition-transform"
                                        :class="{ 'rotate-180': activeDropdown === item.name }"
                                    ></i>
                                </button>
                                <transition
                                    enter-active-class="transition ease-out duration-100"
                                    enter-from-class="transform opacity-0 scale-95"
                                    enter-to-class="transform scale-100"
                                    leave-active-class="transition ease-in duration-75"
                                    leave-from-class="transform scale-100"
                                    leave-to-class="transform opacity-0 scale-95"
                                >
                                    <div
                                        v-if="activeDropdown === item.name"
                                        class="bg-cold-steel-900 absolute left-0 z-10 mt-2 w-36 origin-top-left rounded-md outline-1 -outline-offset-1 outline-white/10"
                                    >
                                        <template v-for="subItem in item.children">
                                            <Link
                                                v-if="!subItem.authRequired || isLoggedIn"
                                                :key="subItem.name"
                                                :href="route(subItem.hrefRoute)"
                                                class="hover:text-cold-steel-50 block px-4 py-2 text-sm hover:bg-white/5 hover:outline-hidden"
                                                :class="
                                                    route().current(subItem.hrefRoute)
                                                        ? 'text-cold-steel-50 bg-white/5'
                                                        : 'text-cold-steel-100'
                                                "
                                            >
                                                {{ subItem.name }}
                                            </Link>
                                        </template>
                                    </div>
                                </transition>
                            </div>
                        </template>
                    </nav>
                    <form @submit.prevent="form.get(route('search'))" class="hidden w-full flex-1 md:flex md:justify-end">
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
                            <FormButton class="hidden"></FormButton>
                        </div>
                    </form>
                    <nav
                        aria-label="profile"
                        class="hidden grow-0 flex-row items-center justify-end space-x-4 md:ms-3 md:flex"
                    >
                        <ProfileDropdown v-if="isLoggedIn"></ProfileDropdown>
                        <Link
                            v-if="!isLoggedIn"
                            :href="route('login')"
                            :active="route().current('login')"
                            class="text-cold-steel-100 hover:bg-cold-steel-900 hover:text-cold-steel-50 focus:bg-cold-steel-900 block rounded-md px-4 py-3 text-sm font-medium whitespace-nowrap focus:inset-ring-blue-300 md:inline-block md:px-3 md:py-2"
                            :class="{ 'bg-cold-steel-900': route().current('login') }"
                            >Log in</Link
                        >
                        <Link
                            v-if="!isLoggedIn && route().has('register')"
                            :href="route('register')"
                            :active="route().current('register')"
                            class="text-cold-steel-100 hover:bg-cold-steel-900 hover:text-cold-steel-50 focus:bg-cold-steel-900 block rounded-md px-4 py-3 text-sm font-medium whitespace-nowrap focus:inset-ring-blue-300 md:inline-block md:px-3 md:py-2"
                            :class="{ 'bg-cold-steel-900': route().current('register') }"
                            >Register</Link
                        >
                    </nav>
                    <button
                        @click="isMobileMenuOpen = !isMobileMenuOpen"
                        class="hover:bg-cold-steel-900 focus:bg-cold-steel-900 place-self-center rounded-lg p-2 text-gray-400 focus:ring-2 focus:ring-gray-200 focus:outline-none md:hidden"
                        aria-controls="navbar-dropdown"
                        aria-expanded="false"
                    >
                        <span class="sr-only">Open main menu</span>
                        <i class="fa-solid fa-bars fa-xl"></i>
                    </button>
                </div>
                <div
                    v-show="isMobileMenuOpen"
                    class="bg-cold-steel-900 absolute top-11 right-1 left-1 mt-2 origin-top rounded-b-lg px-4 py-2 md:hidden"
                >
                    <nav class="md:hidden" aria-label="main">
                        <template v-for="item in navLinks" :key="item.name">
                            <Link
                                v-if="!item.children"
                                :href="route(item.hrefRoute)"
                                class="text-cold-steel-100 hover:bg-cold-steel-900 hover:text-cold-steel-50 focus:db-cold-steel-900 block rounded-md px-4 py-3 text-sm font-medium focus:inset-ring-blue-300 md:inline-block md:px-3 md:py-2"
                                :class="{ 'bg-cold-steel-900': route().current(item.hrefRoute) }"
                                >Home</Link
                            >
                            <div v-else class="relative">
                                <button
                                    @click="toggleMobileSubMenu(item.name)"
                                    class="text-cold-steel-100 hover:bg-cold-steel-900 hover:text-cold-steel-50 focus:bg-cold-steel-900 flex w-full items-center justify-between rounded-md px-4 py-3 text-left text-sm font-medium whitespace-nowrap focus:inset-ring-blue-300"
                                    :class="{
                                        'bg-cold-steel-900':
                                            activeDropdown === item.name ||
                                            item.activeCondition.some((r) => route().current(r)),
                                    }"
                                    :aria-current="activeDropdown === item.name ? 'page' : 'false'"
                                >
                                    {{ item.name }}
                                    <i
                                        class="fa-solid fa-chevron-down transform place-self-end transition-transform"
                                        :class="{ 'rotate-180': activeMobileMenu === item.name }"
                                    ></i>
                                </button>

                                <div v-if="activeMobileMenu === item.name" class="bg-cold-steel-900 relative mt-1 w-full">
                                    <template v-for="subItem in item.children">
                                        <Link
                                            v-if="!subItem.authRequired || isLoggedIn"
                                            :key="subItem.name"
                                            :href="route(subItem.hrefRoute)"
                                            class="hover:text-cold-steel-50 block px-8 py-2 text-sm hover:bg-white/5 hover:outline-hidden"
                                            :class="
                                                route().current(subItem.hrefRoute)
                                                    ? 'text-cold-steel-50 bg-white/5'
                                                    : 'text-cold-steel-100'
                                            "
                                        >
                                            {{ subItem.name }}
                                        </Link>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </nav>
                    <form @submit.prevent="form.get(route('search'))" class="w-full md:hidden">
                        <div
                            class="my-4 grid w-full max-w-full min-w-45 grid-cols-1 rounded-md shadow-sm ring-1 ring-gray-300 ring-inset focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-inset"
                        >
                            <input
                                name="search"
                                type="search"
                                v-model="form.search"
                                placeholder="Search the site..."
                                aria-label="Search"
                                class="placeholder:text-cold-steel-200 col-start-1 row-start-1 block w-full rounded-md border-0 bg-gray-50 py-1.5 pr-3 pl-10 text-sm leading-6 text-gray-900 outline-none focus:ring-0"
                            />
                            <i
                                class="fa-solid fa-magnifying-glass text-cold-steel-200 pointer-events-none col-start-1 row-start-1 ml-3 block h-5 w-5 self-center align-middle leading-6"
                            ></i>
                            <FormButton class="hidden"></FormButton>
                        </div>
                    </form>
                    <nav aria-label="profile">
                        <template v-if="isLoggedIn">
                            <div class="relative">
                                <button class="item-center mx-3 my-2 hidden rounded-full p-1 md:inline-flex">
                                    <img :src="$page.props.auth.user.gravatar_url" alt="" class="size-10 rounded-full" />
                                </button>
                                <div class="flex w-full justify-around gap-2 md:hidden">
                                    <Link
                                        :href="route('dashboard')"
                                        class="block px-4 py-2 text-sm text-gray-300"
                                        :class="{ 'bg-white/5 text-white': route().current('dashboard') }"
                                    >
                                        Dashboard
                                    </Link>
                                    <Link
                                        :href="route('dashboard')"
                                        class="block px-4 py-2 text-sm text-gray-300"
                                        :class="{ 'bg-white/5 text-white': route().current('profile.edit') }"
                                    >
                                        Profile
                                    </Link>
                                    <Form method="POST" :action="route('logout')">
                                        <button type="submit" class="block px-4 py-2 text-sm text-gray-300">Log out</button>
                                    </Form>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <Link
                                :href="route('login')"
                                :active="route().current('login')"
                                class="text-cold-steel-100 hover:bg-cold-steel-900 hover:text-cold-steel-50 focus:bg-cold-steel-900 mx-auto block rounded-md px-4 py-3 text-center text-sm font-medium whitespace-nowrap focus:inset-ring-blue-300 md:inline-block md:px-3 md:py-2"
                                :class="{ 'bg-cold-steel-900': route().current('login') }"
                                >Log in</Link
                            >
                        </template>
                    </nav>
                </div>
            </div>
        </div>
    </header>
</template>
