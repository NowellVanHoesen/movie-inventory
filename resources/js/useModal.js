import { router } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ref } from "vue";

const modal = ref(null);

const setModal = (data) => {
    if (modal.value) {
        return;
    }

    resolvePageComponent(`./Pages/${data.component}.vue`, import.meta.glob("./Pages/**/*.vue")).then((component) => {
        modal.value = data;
        modal.value.resolvedComponent = component;
        modal.value.show = true;
    });
};

const open = (href) => {
    router.visit(href, {
        preserveState: true,
        preserveScroll: true,
        except: ["movies"],
    });
};

const close = () => {
    if (modal.value) {
        modal.value.show = false;
    }
};

const reset = () => {
    if (modal.value?.baseUrl && modal.value.baseUrl !== window.location.href) {
        router.visit(modal.value.baseUrl, {
            preserveState: true,
            preserveScroll: true,
            except: ["movies"],
        });
    }

    modal.value = null;
};

export { close, modal, open, reset, setModal };
