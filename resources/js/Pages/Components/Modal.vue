<script setup>
import { onMounted, onUnmounted, watch } from "vue";

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    closeManually: {
        type: Boolean,
        required: false,
        default: false,
    },
});

const emit = defineEmits(["close", "after-leave"]);

watch(
    () => props.show,
    (show) => {
        if (show) {
            const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
            document.body.style.overflow = "hidden";
            document.body.style.paddingRight = `${scrollbarWidth}px`;
        } else {
            document.body.style.overflow = "";
            document.body.style.paddingRight = "";
        }
    },
    { immediate: true },
);

const close = () => emit("close");

const closeOnEscape = (event) => {
    if (!props.show || event.key !== "Escape") return;

    const focusedElementName = event.target.nodeName;

    if (["INPUT", "SELECT", "TEXTAREA"].includes(focusedElementName)) return;

    close();
};

if (!props.closeManually) {
    onMounted(() => document.addEventListener("keydown", closeOnEscape));
    onUnmounted(() => document.removeEventListener("keydown", closeOnEscape));
}
</script>

<template>
    <teleport to="body">
        <transition leave-active-class="transition duration-300" @after-leave="$emit('after-leave')">
            <div v-show="show" dusk="modal-wrapper" class="fixed inset-0 z-75 flex size-full items-center justify-center">
                <transition
                    appear
                    enter-from-class="opacity-0"
                    enter-to-class="opacity-100"
                    enter-active-class="transition duration-300"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                    leave-active-class="transition duration-200"
                >
                    <div
                        v-show="show"
                        @click="closeManually ? null : close()"
                        class="fixed inset-0 z-400 size-full bg-black/75"
                    />
                </transition>
                <transition
                    appear
                    enter-from-class="opacity-0 scale-90"
                    enter-to-class="opacity-100 scale-100"
                    enter-active-class="transition duration-300"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-90"
                    leave-active-class="transition duration-200"
                >
                    <div
                        v-show="show"
                        class="z-500 max-h-[calc(100vh-2rem)] w-full overflow-auto rounded-lg p-4 shadow-lg sm:max-w-6xl md:p-6"
                    >
                        <slot />
                    </div>
                </transition>
            </div>
        </transition>
    </teleport>
</template>
