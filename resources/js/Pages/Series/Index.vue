<script setup>
import Layout from '../../layouts/Layout.vue';
import SeriesPoster from '../Components/SeriesPoster.vue';
import BackToTop from '../Components/BackToTop.vue';
import { InfiniteScroll, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
	series: {
		type: Object,
		required: true,
	},
	page_title: {
		type: String,
		default: "Series",
	},
});
</script>

<template>
	<Layout :heading="page_title">
		<InfiniteScroll
			class="mt-6 grid grid-cols-[repeat(auto-fill,minmax(187px,1fr))] place-items-center gap-4"
			data="series"
			:buffer="50"
		>
			<SeriesPoster v-for="show in series.data" :key="show.slug" :series="show" />
		</InfiniteScroll>
		<BackToTop />
	</Layout>
</template>