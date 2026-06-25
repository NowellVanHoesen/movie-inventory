<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    active: {
        type: Boolean,
        default: false,
    },
    preserveState: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <Link
        :class="
            'text-cold-steel-100 hover:bg-cold-steel-900 focus:bg-cold-steel-900 hover:text-cold-steel-50 block rounded-md px-4 py-3 text-sm font-medium focus:ring focus:inset-ring-blue-300 md:inline-block md:px-3 md:py-2' +
            (active ? ' bg-cold-steel-900' : '')
        "
        :aria-current="active ? 'page' : 'false'"
        v-bind="$attrs"
        preserve-state="preserveState"
    >
        <slot />
    </Link>
</template>
