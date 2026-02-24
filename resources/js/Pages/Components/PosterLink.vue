<script setup>
import { Link } from '@inertiajs/vue3';

</script>

<template>
    <Link v-bind="$attrs" class="group relative mb-auto block max-w-fit rounded-xl">
        <slot />
    </Link>
</template>
