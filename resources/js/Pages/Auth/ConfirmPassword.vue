<script setup>
import Layout from "@/Layouts/Layout.vue";
import FormButton from "../Components/FormButton.vue";
import { useForm } from "@inertiajs/vue3";

const form = useForm({
    password: "",
});

const submit = () => {
    form.post(route("password.confirm"), {
        onFinish: () => {
            form.reset("password");
        },
    });
};
</script>

<template>
    <Layout heading="Confirm Password">
        <div class="relative mx-auto max-w-md rounded-xl bg-white/80 p-6 text-gray-900">
            <p class="text-sm text-gray-600">
                This is a secure area of the application. Please confirm your password before continuing.
            </p>

            <form class="mt-4" @submit.prevent="submit">
                <div>
                    <label for="password" class="block text-sm font-bold">Password</label>
                    <input
                        id="password"
                        type="password"
                        v-model="form.password"
                        required
                        autofocus
                        autocomplete="current-password"
                        dusk="confirm-password-input"
                        class="border-cold-steel-300 mt-1 w-full rounded-md border px-2 py-1 text-sm"
                    />
                    <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
                </div>

                <div class="mt-6 flex items-center justify-end gap-4">
                    <FormButton :disabled="form.processing" dusk="confirm-password-submit-btn">Confirm</FormButton>
                </div>
            </form>
        </div>
    </Layout>
</template>
