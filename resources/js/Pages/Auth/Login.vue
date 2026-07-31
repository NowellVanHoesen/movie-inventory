<script setup>
import Modal from "../Components/Modal.vue";
import FormButton from "../Components/FormButton.vue";
import { useForm } from "@inertiajs/vue3";

defineProps({
    status: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(["close"]);

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const closeModal = () => {
    emit("close");
};

const submit = () => {
    form.post(route("login"), {
        preserveScroll: true,
        onSuccess: () => {
            emit("close");
        },
        onFinish: () => {
            form.reset("password");
        },
    });
};
</script>

<template>
    <Modal @close="closeModal">
        <div class="relative mx-auto max-w-md rounded-xl bg-white/80 p-6 text-gray-900">
            <button
                type="button"
                class="text-cold-steel-600 hover:bg-cold-steel-600 focus:bg-cold-steel-600 absolute top-2 right-2 cursor-pointer rounded-md border border-transparent px-2 py-1 font-semibold transition duration-150 ease-in-out hover:text-white focus:text-white focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 focus:outline-none"
                @click="closeModal"
            >
                <i class="fa-solid fa-xmark fa-lg"></i>
            </button>

            <h2 class="text-2xl">Log In</h2>

            <p v-if="status" class="mt-4 text-sm font-medium text-green-600">{{ status }}</p>

            <form class="mt-4" @submit.prevent="submit">
                <div>
                    <label for="email" class="block text-sm font-bold">Email</label>
                    <input
                        id="email"
                        type="email"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        dusk="login-email-input"
                        class="border-cold-steel-300 mt-1 w-full rounded-md border px-2 py-1 text-sm"
                    />
                    <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                </div>

                <div class="mt-4">
                    <label for="password" class="block text-sm font-bold">Password</label>
                    <input
                        id="password"
                        type="password"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        dusk="login-password-input"
                        class="border-cold-steel-300 mt-1 w-full rounded-md border px-2 py-1 text-sm"
                    />
                    <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" v-model="form.remember" dusk="login-remember-input" />
                        Remember me
                    </label>

                    <a
                        :href="route('password.request')"
                        class="text-cold-steel-600 text-sm font-semibold hover:underline"
                    >
                        Forgot your password?
                    </a>
                </div>

                <div class="mt-6 flex items-center gap-4">
                    <FormButton :disabled="form.processing" dusk="login-submit-btn">Log In</FormButton>
                    <button
                        type="button"
                        dusk="login-cancel-btn"
                        class="text-cold-steel-600 text-sm font-semibold hover:underline"
                        @click="closeModal"
                    >
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </Modal>
</template>
