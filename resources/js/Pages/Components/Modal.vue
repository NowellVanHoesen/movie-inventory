<script setup>
import { onMounted, onUnmounted } from "vue";

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

const emit = defineEmits(["close"]);

function close() {
    emit("close");
}

function closeOnEscape(event) {
    if (!props.show || event.key !== "Escape") return;

    const focusedElementName = event.target.nodeName;

    if (["INPUT", "SELECT", "TEXTAREA"].includes(focusedElementName)) return;

    close();
}

if (!props.closeManually) {
    onMounted(() => document.addEventListener("keydown", closeOnEscape));
    onUnmounted(() => document.removeEventListener("keydown", closeOnEscape));
}
</script>

<template>
    <Teleport to="body">
        <Transition leave-active-class="transition duration-300">
            <div
                v-show="show"
                id="modal-wrapper"
                class="fixed inset-0 z-50 flex size-full items-center justify-center"
                @click="closeManually ? null : close()"
            >
                <Transition
                    enter-from-class="opacity-0"
                    enter-to-class="opacity-100"
                    enter-active-class="transition duration-300"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                    leave-active-class="transition duration-200"
                >
                    <div v-show="show" @click="close" class="fixed inset-0 z-40 size-full bg-black/75" />
                </Transition>
                <Transition
                    enter-from-class="opacity-0 scale-90"
                    enter-to-class="opacity-100 scale-100"
                    enter-active-class="transition duration-300"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-90"
                    leave-active-class="transition duration-200"
                >
                    <div
                        v-show="show"
                        class="bg-cold-steel-800 z-50 max-h-[calc(100vh-2rem)] w-full overflow-auto rounded-lg p-4 shadow-lg sm:max-w-6xl md:p-6"
                    >
                        <slot />
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
