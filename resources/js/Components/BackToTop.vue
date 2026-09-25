<script setup>
import { onMounted, onUnmounted, ref } from "vue";

const showButton = ref(false);

const updateVisibility = () => {
    showButton.value = window.scrollY > 400;
};

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
};

onMounted(() => window.addEventListener("scroll", updateVisibility, { passive: true }));
onUnmounted(() => window.removeEventListener("scroll", updateVisibility));
</script>

<template>
    <Transition
        enter-active-class="transition duration-200"
        enter-from-class="opacity-0 translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-150"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-2"
    >
        <button
            v-if="showButton"
            type="button"
            @click="scrollToTop"
            dusk="back-to-top-btn"
            aria-label="Back to top"
            class="bg-cold-steel-700 hover:bg-cold-steel-900 text-cold-steel-100 fixed right-4 bottom-4 z-40 flex size-12 items-center justify-center rounded-full shadow-lg transition-colors focus:ring-2 focus:ring-white/50 focus:outline-none md:right-8 md:bottom-8"
        >
            <i class="fa-solid fa-arrow-up"></i>
        </button>
    </Transition>
</template>
