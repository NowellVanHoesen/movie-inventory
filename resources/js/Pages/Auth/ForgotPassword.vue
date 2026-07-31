<script setup>
import Layout from "@/Layouts/Layout.vue";
import FormButton from "../Components/FormButton.vue";
import { useForm } from "@inertiajs/vue3";

defineProps({
    status: {
        type: String,
        default: null,
    },
});

const form = useForm({
    email: "",
});

const submit = () => {
    form.post(route("password.email"));
};
</script>

<template>
    <Layout heading="Forgot Password">
        <div class="relative mx-auto max-w-md rounded-xl bg-white/80 p-6 text-gray-900">
            <p class="text-sm text-gray-600">
                Forgot your password? No problem. Just let us know your email address and we will email you a
                password reset link that will allow you to choose a new one.
            </p>

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
                        dusk="forgot-password-email-input"
                        class="border-cold-steel-300 mt-1 w-full rounded-md border px-2 py-1 text-sm"
                    />
                    <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                </div>

                <div class="mt-6 flex items-center gap-4">
                    <FormButton :disabled="form.processing" dusk="forgot-password-submit-btn">
                        Email Password Reset Link
                    </FormButton>
                </div>
            </form>
        </div>
    </Layout>
</template>
