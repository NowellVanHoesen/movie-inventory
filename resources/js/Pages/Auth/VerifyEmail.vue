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

const resendForm = useForm({});

const resend = () => {
    resendForm.post(route("verification.send"));
};

const logoutForm = useForm({});

const logout = () => {
    logoutForm.post(route("logout"));
};
</script>

<template>
    <Layout heading="Verify Email">
        <div class="relative mx-auto max-w-md rounded-xl bg-white/80 p-6 text-gray-900">
            <p class="text-sm text-gray-600">
                Thanks for signing up! Before getting started, could you verify your email address by clicking on the
                link we just emailed to you? If you didn't receive the email, we will gladly send you another.
            </p>

            <p v-if="status === 'verification-link-sent'" class="mt-4 text-sm font-medium text-green-600">
                A new verification link has been sent to the email address you provided during registration.
            </p>

            <div class="mt-6 flex items-center justify-between">
                <form @submit.prevent="resend">
                    <FormButton :disabled="resendForm.processing" dusk="resend-verification-btn">
                        Resend Verification Email
                    </FormButton>
                </form>

                <form @submit.prevent="logout">
                    <button
                        type="submit"
                        dusk="verify-email-logout-btn"
                        class="text-cold-steel-600 text-sm font-semibold hover:underline"
                    >
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </Layout>
</template>
