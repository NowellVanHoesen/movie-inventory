<script setup>
import { nextTick, onMounted, onUnmounted, ref, watch } from "vue";

// A confirmation dialog meant to stack on top of an already-open Modal. It
// deliberately doesn't reuse Modal.vue: that component owns the body scroll lock,
// and a second instance would clobber it on open/close while the modal beneath
// is still showing.
const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        required: true,
    },
    message: {
        type: String,
        default: "",
    },
    confirmLabel: {
        type: String,
        default: "Delete",
    },
    processing: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["confirm", "cancel"]);

const cancelButton = ref(null);

watch(
    () => props.show,
    (show) => {
        if (show) {
            nextTick(() => cancelButton.value?.focus());
        }
    },
);

const cancel = () => {
    if (!props.processing) {
        emit("cancel");
    }
};

// Listen in the capture phase and stop the event there, so Escape only dismisses
// this dialog and never reaches the underlying Modal's own (bubble-phase) handler.
const cancelOnEscape = (event) => {
    if (props.show && event.key === "Escape") {
        event.stopImmediatePropagation();
        cancel();
    }
};

onMounted(() => document.addEventListener("keydown", cancelOnEscape, true));
onUnmounted(() => document.removeEventListener("keydown", cancelOnEscape, true));
</script>

<template>
    <teleport to="body">
        <transition
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            enter-active-class="transition duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
            leave-active-class="transition duration-150"
        >
            <div
                v-if="show"
                dusk="confirm-dialog"
                role="alertdialog"
                aria-modal="true"
                aria-labelledby="confirm-dialog-title"
                class="fixed inset-0 z-600 flex items-center justify-center p-4"
            >
                <div class="fixed inset-0 bg-black/50" @click="cancel" />
                <div class="relative w-full max-w-md rounded-xl bg-white/90 p-6 text-gray-900 shadow-lg">
                    <h2 id="confirm-dialog-title" class="text-lg font-medium text-gray-900">{{ title }}</h2>
                    <p v-if="message" class="mt-1 text-sm text-gray-600">{{ message }}</p>
                    <div class="mt-6 flex justify-end gap-4">
                        <button
                            ref="cancelButton"
                            type="button"
                            dusk="confirm-dialog-cancel"
                            class="text-cold-steel-600 cursor-pointer rounded-md px-4 py-2 text-sm font-semibold hover:underline focus:ring-2 focus:ring-blue-300 focus:outline-none"
                            :disabled="processing"
                            @click="cancel"
                        >
                            Cancel
                        </button>
                        <button
                            type="button"
                            dusk="confirm-dialog-confirm"
                            class="inline-flex cursor-pointer items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-sm font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out hover:bg-red-500 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:outline-none active:bg-red-700 disabled:opacity-50"
                            :disabled="processing"
                            @click="emit('confirm')"
                        >
                            {{ confirmLabel }}
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </teleport>
</template>
