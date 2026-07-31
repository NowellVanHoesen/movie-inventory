<script setup>
import Layout from "@/Layouts/Layout.vue";
import FormButton from "../Components/FormButton.vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    token: {
        type: String,
        required: true,
    },
    email: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("password.store"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <Layout heading="Reset Password">
        <div class="relative mx-auto max-w-md rounded-xl bg-white/80 p-6 text-gray-900">
            <form @submit.prevent="submit">
                <div>
                    <label for="email" class="block text-sm font-bold">Email</label>
                    <input
                        id="email"
                        type="email"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        dusk="reset-password-email-input"
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
                        autocomplete="new-password"
                        dusk="reset-password-password-input"
                        class="border-cold-steel-300 mt-1 w-full rounded-md border px-2 py-1 text-sm"
                    />
                    <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
                </div>

                <div class="mt-4">
                    <label for="password_confirmation" class="block text-sm font-bold">Confirm Password</label>
                    <input
                        id="password_confirmation"
                        type="password"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        dusk="reset-password-password-confirmation-input"
                        class="border-cold-steel-300 mt-1 w-full rounded-md border px-2 py-1 text-sm"
                    />
                    <p v-if="form.errors.password_confirmation" class="mt-1 text-sm text-red-600">
                        {{ form.errors.password_confirmation }}
                    </p>
                </div>

                <div class="mt-6 flex items-center gap-4">
                    <FormButton :disabled="form.processing" dusk="reset-password-submit-btn">
                        Reset Password
                    </FormButton>
                </div>
            </form>
        </div>
    </Layout>
</template>
