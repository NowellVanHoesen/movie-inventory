<script setup>
import Footer from "../Pages/Components/Footer.vue";
import Header from "../Pages/Components/Header.vue";
import BaseModal from "../Pages/Components/BaseModal.vue";
import { computed } from "vue";

const props = defineProps({
    heading: {
        type: String,
        default: "",
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
    return props.heading ?? page.props.appName;
});
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
                <slot />
            </div>
        </main>
        <Footer />
    </div>
</template>
