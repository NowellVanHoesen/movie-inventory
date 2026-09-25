<script setup>
import { computed, ref, onMounted, onUnmounted } from "vue";
import { Link, usePage, Form } from "@inertiajs/vue3";
import SearchForm from "./SearchForm.vue";
import { open } from "@/useModal.js";

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
                hrefRoute: "movies.create",
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
const isMobile = ref(window.innerWidth < 768);
const isMobileMenuOpen = ref(false);
const isProfileMenuOpen = ref(false);
const activeDropdown = ref(null);
const activeMobileMenu = ref(null);

const updateIsMobile = () => {
    isMobile.value = window.innerWidth < 768;
    if (false === isMobile.value) {
        isMobileMenuOpen.value = false;
    }
};

onMounted(() => window.addEventListener("resize", updateIsMobile));
onUnmounted(() => window.removeEventListener("resize", updateIsMobile));

const toggleDropdown = (menuName) => {
    activeDropdown.value = activeDropdown.value === menuName ? null : menuName;
};

const toggleMobileSubMenu = (menuName) => {
    activeMobileMenu.value = activeMobileMenu.value === menuName ? null : menuName;
};

const closeAllMenus = () => {
    isProfileMenuOpen.value = false;
    isMobileMenuOpen.value = false;
    activeDropdown.value = null;
    activeMobileMenu.value = null;
};

// Deferred so the mobile menu (and the logout <Form> inside it) isn't
// unmounted before the form's submit is actually dispatched.
const closeAllMenusAfterSubmit = () => setTimeout(closeAllMenus, 0);
</script>

<template>
    <header class="bg-cold-steel-700 z-50 shadow-sm">
        <div class="text-cold-steel-100 relative flex h-16 flex-row place-items-center px-4 py-4 md:justify-normal">
            <div class="relative flex w-full md:-left-1 md:mx-auto md:max-w-7xl md:justify-normal md:px-4">
                <div
                    class="relative top-full right-0 left-0 z-15 flex w-full flex-row items-center justify-between gap-4 drop-shadow-lg"
                >
                    <Link :href="route('home')" class="group rounded-md focus:outline-none">
                        <i class="fa-solid fa-clapperboard fa-2xl group-focus-within:text-shadow-[0_0_10px_#bada55]" title="Movie Inventory"></i>
                    </Link>
                    <button
                        @click="isMobileMenuOpen = !isMobileMenuOpen"
                        class="hover:bg-cold-steel-900 focus:bg-cold-steel-900 place-self-center rounded-lg p-2 text-gray-400 focus:ring focus:ring-gray/20 focus:outline-none md:hidden"
                        aria-controls="navbar-dropdown"
                        aria-expanded="false"
"
                    >
                        <span class="sr-only">Open main menu</span>
                        <i class="fa-solid fa-bars fa-xl"></i>
                    </button>
                    <div
                        v-if="(isMobile && isMobileMenuOpen) || !isMobile"
                        class="flex flex-col md:grow md:flex-row"
                        :class="{
                            'bg-cold-steel-900 absolute top-11 right-0 left-0 mt-2 origin-top rounded-b-lg pb-4': isMobile,
                        }"
                    >
                        <nav class="md:flex md:grow md:items-center md:gap-4" aria-label="main">
                            <template v-for="item in navLinks" :key="item.name">
                                <Link
                                    v-if="!item.children"
                                    :href="route(item.hrefRoute)"
                                    class="text-cold-steel-100 hover:text-cold-steel-50 focus:db-cold-steel-900 block md:rounded-md px-6 py-4 hover:bg-white/10 focus:ring-white/25  focus:outline-none focus:ring md:inline-block md:px-3 md:py-2"
                                    :class="{ 'bg-white/5': route().current(item.hrefRoute) }"
                                    >{{ item.name }}</Link
                                >
                                <div v-else class="relative last:[&>*:first-child]:rounded-b-md">
                                    <button
                                        @click="toggleDropdown(item.name)"
                                        :dusk="`nav-dropdown-${item.name}`"
                                        class="text-cold-steel-100 hover:text-cold-steel-50 flex w-full items-center justify-between px-6 py-4 whitespace-nowrap hover:bg-white/10 focus:ring-white/25 focus:ring md:inline-block focus:outline-none md:w-auto md:rounded-md md:px-3 md:py-2 "
                                        :class="[
                                            {
                                                'bg-white/5':
                                                    activeDropdown === item.name ||
                                                    item.activeCondition.some((r) => route().current(r)),
                                            }
                                        ]"
                                        :aria-current="activeDropdown === item.name ? 'page' : 'false'"
                                    >
                                        {{ item.name }}
                                        <i
                                            class="fa-solid fa-chevron-down transform self-center place-self-end transition-transform"
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
                                            v-show="activeDropdown === item.name || activeMobileMenu === item.name"
                                            class="bg-cold-steel-900 relative w-full md:absolute md:left-0 md:z-10 md:mt-2 md:w-36 md:origin-top-left md:rounded-md md:outline-1 md:-outline-offset-1 md:outline-white/10"
                                        >
                                            <template v-for="subItem in item.children">
                                                <Link
                                                    v-if="!subItem.authRequired || isLoggedIn"
                                                    :key="subItem.name"
                                                    :href="route(subItem.hrefRoute)"
                                                    :dusk="`nav-link-${subItem.hrefRoute}`"
                                                    class="hover:text-cold-steel-50 block px-10 py-2 text-sm hover:bg-white/10 focus:outline-none md:px-4"
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
                        <SearchForm />
                        <nav
                            aria-label="profile"
                            class="md:grow-0 md:flex-row md:items-center md:justify-end md:space-x-4 md:ms-3 md:flex relative"
                        >
                            <template v-if="isLoggedIn">
                                <div class="relative">
                                    <button
                                        @click="isProfileMenuOpen = !isProfileMenuOpen"
                                        class="item-center mx-3 my-2 hidden rounded-full p-1 md:inline-flex"
                                    >
                                        <img :src="$page.props.auth.user.gravatar_url" alt="" class="size-10 rounded-full" />
                                    </button>
                                    <div
                                        v-show="isMobile || isProfileMenuOpen"
                                        class="flex w-full justify-around md:absolute md:z-10 md:origin-top-right md:right-0 md:w-36 md:rounded-md md:outline-1 md:-outline-offset-1 md:outline-white/10 md:flex-col md:bg-cold-steel-900"
                                    >
                                        <Link
                                            :href="route('dashboard')"
                                            class="hover:text-cold-steel-50 block px-4 py-2 text-sm hover:bg-white/10 focus:outline-none"
                                            :class="
                                                route().current('dashboard')
                                                    ? 'text-cold-steel-50 bg-white/5'
                                                    : 'text-cold-steel-100'
                                            "
                                        >
                                            Dashboard
                                        </Link>
                                        <Link
                                            :href="route('profile.edit')"
                                            class="hover:text-cold-steel-50 block px-4 py-2 text-sm hover:bg-white/10 focus:outline-none"
                                            :class="
                                                route().current('profile.edit')
                                                    ? 'text-cold-steel-50 bg-white/5'
                                                    : 'text-cold-steel-100'
                                            "
                                        >
                                            Profile
                                        </Link>
                                        <Form method="POST" :action="route('logout')">
                                            <button @click="closeAllMenusAfterSubmit" type="submit" class="hover:text-cold-steel-50 block px-4 py-2 text-sm hover:bg-white/10 focus:outline-none md:w-full md:text-left">Log out</button>
                                        </Form>
                                    </div>
                                </div>
                            </template>
                            <template v-else>
                                <button
                                    type="button"
                                    dusk="login-nav-btn"
                                    @click="open(route('login'))"
                                    class="text-cold-steel-100 hover:bg-cold-steel-900 hover:text-cold-steel-50 focus:bg-cold-steel-900 mx-auto block rounded-md px-4 py-3 text-center text-sm font-medium whitespace-nowrap focus:inset-ring-blue-300 md:inline-block md:px-3 md:py-2"
                                >
                                    Log in
                                </button>
                            </template>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>
