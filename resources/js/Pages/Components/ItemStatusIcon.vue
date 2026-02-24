<script setup>
	defineProps({
		purchased: {
			type: Boolean,
			default: false,
		},
	})
</script>

<template>
	<i :class="'absolute top-4 right-2 fa-solid fa-lg text-shadow-lg/30 ' + ( purchased ? 'fa-circle-check text-green-check' : 'fa-heart text-red-heart')"></i>
</template>
