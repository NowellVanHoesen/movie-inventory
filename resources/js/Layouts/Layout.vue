<script setup>
import Footer from "@/Components/Footer.vue";
import Header from "@/Components/Header.vue";
import BaseModal from "@/Components/BaseModal.vue";
import { usePage } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";

const page = usePage();

const props = defineProps({
    heading: {
        type: String,
        default: null,
    },
    backdrop: {
        type: String,
        default: null,
    },
});

const bgStyle = computed(() => {
    if ( !props.backdrop ) {
        return {};
    }

    return {
        backgroundImage: `linear-gradient(rgba(255,255,255,0.5), rgba(255,255,255,0.5)), url(https://image.tmdb.org/t/p/original${props.backdrop})`
    };
});

const heading = computed(() => {
    return props.heading || page.props.appName;
});

const flashMessage = ref(null);
let flashTimer = null;

const dismissFlash = () => {
    clearTimeout(flashTimer);
    flashMessage.value = null;
};

watch(
    () => page.props.flash?.message,
    (message) => {
        if (!message) {
            return;
        }

        clearTimeout(flashTimer);
        flashMessage.value = message;
        flashTimer = setTimeout(dismissFlash, 5000);
    },
    { immediate: true },
);
</script>

<template>
    <BaseModal />
    <div class="flex h-screen flex-col">
        <Header />
        <main
            class="text-cold-steel-100 bg-cold-steel-600 relative mb-auto w-full flex-1 grow bg-cover bg-fixed bg-top bg-no-repeat"
            :style="bgStyle"
        >
            <div class="relative mx-auto max-w-7xl p-2 lg:px-6">
                <h1
                    class="text-3xl font-bold tracking-tight mx-auto mb-4 w-full p-4"
                    :class="backdrop ? 'bg-white/50 rounded-2xl text-cold-steel-600' : 'text-cold-steel-50'"
                >
                    {{ heading }}
                </h1>
                <div
                    v-if="flashMessage"
                    dusk="flash-message"
                    role="status"
                    class="bg-green-check text-cold-steel-800 mx-auto mb-4 flex w-full items-center justify-between rounded-lg px-4 py-3 text-sm font-semibold"
                >
                    <span>{{ flashMessage }}</span>
                    <button
                        type="button"
                        class="cursor-pointer px-2"
                        aria-label="Dismiss"
                        @click="dismissFlash"
                    >
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <slot />
            </div>
        </main>
        <Footer />
    </div>
</template>
