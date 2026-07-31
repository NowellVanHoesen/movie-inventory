<script setup>
import { useForm } from "@inertiajs/vue3";
import FormButton from "../../Components/FormButton.vue";

defineProps({
    status: {
        type: String,
        default: null,
    },
});

const form = useForm({
    current_password: "",
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.put(route("password.update"), {
        errorBag: "updatePassword",
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset("password", "password_confirmation");
            }
            if (form.errors.current_password) {
                form.reset("current_password");
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">Update Password</h2>
            <p class="mt-1 text-sm text-gray-600">
                Ensure your account is using a long, random password to stay secure.
            </p>
        </header>

        <form class="mt-6 space-y-6" @submit.prevent="submit">
            <div>
                <label for="current_password" class="block text-sm font-bold">Current Password</label>
                <input
                    id="current_password"
                    type="password"
                    v-model="form.current_password"
                    autocomplete="current-password"
                    dusk="update-password-current-input"
                    class="border-cold-steel-300 mt-1 w-full rounded-md border px-2 py-1 text-sm"
                />
                <p v-if="form.errors.current_password" class="mt-1 text-sm text-red-600">
                    {{ form.errors.current_password }}
                </p>
            </div>

            <div>
                <label for="password" class="block text-sm font-bold">New Password</label>
                <input
                    id="password"
                    type="password"
                    v-model="form.password"
                    autocomplete="new-password"
                    dusk="update-password-new-input"
                    class="border-cold-steel-300 mt-1 w-full rounded-md border px-2 py-1 text-sm"
                />
                <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-bold">Confirm Password</label>
                <input
                    id="password_confirmation"
                    type="password"
                    v-model="form.password_confirmation"
                    autocomplete="new-password"
                    dusk="update-password-confirmation-input"
                    class="border-cold-steel-300 mt-1 w-full rounded-md border px-2 py-1 text-sm"
                />
                <p v-if="form.errors.password_confirmation" class="mt-1 text-sm text-red-600">
                    {{ form.errors.password_confirmation }}
                </p>
            </div>

            <div class="flex items-center gap-4">
                <FormButton :disabled="form.processing" dusk="update-password-submit-btn">Save</FormButton>
                <p v-if="status === 'password-updated'" class="text-sm text-gray-600">Saved.</p>
            </div>
        </form>
    </section>
</template>
