<script setup>
import { ref, nextTick } from "vue";
import { useForm } from "@inertiajs/vue3";
import Modal from "../../Components/Modal.vue";

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: "",
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value?.focus());
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.reset();
};

const deleteUser = () => {
    form.delete(route("profile.destroy"), {
        errorBag: "userDeletion",
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900">Delete Account</h2>
            <p class="mt-1 text-sm text-gray-600">
                Once your account is deleted, all of its resources and data will be permanently deleted. Before
                deleting your account, please download any data or information that you wish to retain.
            </p>
        </header>

        <button
            type="button"
            dusk="delete-account-btn"
            class="inline-flex cursor-pointer items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-sm font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out hover:bg-red-500 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:outline-none active:bg-red-700"
            @click="confirmUserDeletion"
        >
            Delete Account
        </button>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="relative mx-auto max-w-md rounded-xl bg-white/80 p-6 text-gray-900">
                <h2 class="text-lg font-medium text-gray-900">Are you sure you want to delete your account?</h2>

                <p class="mt-1 text-sm text-gray-600">
                    Once your account is deleted, all of its resources and data will be permanently deleted. Please
                    enter your password to confirm you would like to permanently delete your account.
                </p>

                <form class="mt-6" @submit.prevent="deleteUser">
                    <label for="delete_password" class="sr-only">Password</label>
                    <input
                        id="delete_password"
                        ref="passwordInput"
                        type="password"
                        v-model="form.password"
                        placeholder="Password"
                        dusk="delete-account-password-input"
                        class="border-cold-steel-300 mt-1 block w-3/4 rounded-md border px-2 py-1 text-sm"
                    />
                    <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{ form.errors.password }}</p>

                    <div class="mt-6 flex justify-end gap-3">
                        <button
                            type="button"
                            dusk="delete-account-cancel-btn"
                            class="text-cold-steel-600 text-sm font-semibold hover:underline"
                            @click="closeModal"
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            dusk="delete-account-confirm-btn"
                            class="inline-flex cursor-pointer items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-sm font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out hover:bg-red-500 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:outline-none active:bg-red-700"
                        >
                            Delete Account
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </section>
</template>
