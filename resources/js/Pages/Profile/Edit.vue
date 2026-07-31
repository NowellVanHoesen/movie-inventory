<script setup>
import Layout from "@/Layouts/Layout.vue";
import UpdateProfileInformationForm from "./Partials/UpdateProfileInformationForm.vue";
import UpdatePasswordForm from "./Partials/UpdatePasswordForm.vue";
import DeleteUserForm from "./Partials/DeleteUserForm.vue";

defineProps({
    status: {
        type: String,
        default: null,
    },
});
</script>

<template>
    <Layout heading="Profile">
        <div class="mx-auto max-w-xl space-y-6">
            <div class="rounded-xl bg-white/80 p-4 text-gray-900 sm:p-8">
                <UpdateProfileInformationForm :status="status" />
            </div>

            <div class="rounded-xl bg-white/80 p-4 text-gray-900 sm:p-8">
                <UpdatePasswordForm :status="status" />
            </div>

            <div class="rounded-xl bg-white/80 p-4 text-gray-900 sm:p-8">
                <DeleteUserForm />
            </div>
        </div>
    </Layout>
</template>
