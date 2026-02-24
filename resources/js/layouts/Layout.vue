<script setup>
import Footer from "../Pages/Components/Footer.vue";
import Navigation from "../Pages/Components/Navigation.vue";
import { computed } from "vue";

const props = defineProps({
    heading: {
        type: String,
        default: "",
    },
});

const heading = computed(() => {
    return props.heading ?? page.props.appName;
});
</script>

<template>
    <div class="flex h-screen flex-col">
        <Navigation />
        <main
            class="text-cold-steel-100 bg-cold-steel-600 relative mb-auto flex-1 grow bg-cover bg-fixed bg-top bg-no-repeat"
        >
            <div class="bg-cold-steel-600/75 relative mx-auto max-w-7xl p-2 lg:px-6">
                <div class="text-cold-steel-50 mx-auto mb-4 flex max-w-7xl justify-between py-6">
                    <h1 class="text-3xl font-bold tracking-tight">
                        {{ heading }}
                    </h1>
                </div>
                <slot />
            </div>
        </main>
        <Footer />
    </div>
</template>
