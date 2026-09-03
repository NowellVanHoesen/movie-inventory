import { router } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ref } from "vue";

const modal = ref(null);

// The URL of the page a modal is layered on top of. The server needs this to
// know which route to render as the background; we send it explicitly on every
// modal request (see `X-Modal-Base-Url` below) instead of leaving the server to
// guess from the `Referer` header, which is unreliable across client-side
// navigation.
const modalBaseUrl = () => modal.value?.baseUrl ?? window.location.href;

const setModal = (data) => {
    resolvePageComponent(`./Pages/${data.component}.vue`, import.meta.glob("./Pages/**/*.vue")).then((component) => {
        modal.value = {
            ...data,
            // Keep the URL captured when the modal first opened so `reset()` can
            // tell whether it was dismissed in place or navigated away from.
            url: modal.value?.url ?? window.location.href,
            resolvedComponent: component,
            show: true,
        };
    });
};

const open = (href) => {
    router.visit(href, {
        preserveState: true,
        preserveScroll: true,
        except: ["movies"],
        headers: {
            "X-Modal-Base-Url": modalBaseUrl(),
        },
    });
};

const close = () => {
    if (modal.value) {
        modal.value.show = false;
    }
};

const reset = () => {
    const dismissed = modal.value;
    modal.value = null;

    // Only restore the underlying page when the modal was closed in place, i.e.
    // the browser is still sitting on the modal's own URL. If the user navigated
    // away while the modal was open, leave them where they are — otherwise we'd
    // yank them back to whatever page the modal was originally opened from.
    if (dismissed?.baseUrl && dismissed.url === window.location.href && dismissed.baseUrl !== window.location.href) {
        router.visit(dismissed.baseUrl, {
            preserveState: true,
            preserveScroll: true,
            except: ["movies"],
        });
    }
};

export { close, modal, modalBaseUrl, open, reset, setModal };
