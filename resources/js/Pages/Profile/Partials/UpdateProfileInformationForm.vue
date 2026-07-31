<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import FormButton from "../../Components/FormButton.vue";

defineProps({
    status: {
        type: String,
        default: null,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});

const submit = () => {
    form.patch(route("profile.update"), {
        preserveScroll: true,
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">Profile Information</h2>
            <p class="mt-1 text-sm text-gray-600">Update your account's profile information and email address.</p>
        </header>

        <form class="mt-6 space-y-6" @submit.prevent="submit">
            <div>
                <label for="name" class="block text-sm font-bold">Name</label>
                <input
                    id="name"
                    type="text"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    dusk="profile-name-input"
                    class="border-cold-steel-300 mt-1 w-full rounded-md border px-2 py-1 text-sm"
                />
                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
            </div>

            <div>
                <label for="email" class="block text-sm font-bold">Email</label>
                <input
                    id="email"
                    type="email"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    dusk="profile-email-input"
                    class="border-cold-steel-300 mt-1 w-full rounded-md border px-2 py-1 text-sm"
                />
                <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
            </div>

            <div class="flex items-center gap-4">
                <FormButton :disabled="form.processing" dusk="profile-submit-btn">Save</FormButton>
                <p v-if="status === 'profile-updated'" class="text-sm text-gray-600">Saved.</p>
            </div>
        </form>
    </section>
</template>
