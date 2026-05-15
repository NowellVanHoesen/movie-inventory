import { router } from "@inertiajs/vue3";
import axios from "axios";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { nextTick, ref } from "vue";

const modal = ref(null);

const setModal = (data) => {
    if (modal.value) {
        return;
    }

    resolvePageComponent(`./Pages/${data.component}.vue`, import.meta.glob("./Pages/**/*.vue")).then((component) => {
        modal.value = data;
        modal.value.resolvedComponent = component;
        nextTick(() => {
            modal.value.show = true;
        });
    });
};

const open = (href) => {
    axios
        .get(href, {
            headers: {
                "X-Inertia": true,
                "X-Modal": true,
            },
        })
        .then((response) => setModal(response.data));
};

const close = () => {
    if (modal.value) {
        modal.value.show = false;
    }
};

const reset = () => {
    if (modal.value?.baseUrl && modal.value.baseUrl !== window.location.href) {
        router.visit(modal.value.baseUrl);
    }

    modal.value = null;
};

export { close, modal, open, reset, setModal };
