<script setup>
import Layout from '../../layouts/Layout.vue';
import SeriesPoster from '../Components/SeriesPoster.vue';
import FilterDropdown from '../Components/FilterDropdown.vue';
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
	genres: {
		type: Array,
		required: false,
		default: () => [],
	},
});

const page = usePage();

const applyFilters = () => {
	router.get(
		page.url,
		{},
		{
			reset: ['series'],
			only: ['series'],
		},
	);
};
</script>

<template>
	<Layout :heading="page_title">
		<FilterDropdown
			:genres="genres"
			:sort-options="[
				{ value: 'name_sortable', label: 'Name' },
				{ value: 'first_air_date', label: 'First Air Date' },
				{ value: 'purchase_date', label: 'Purchase Date' },
			]"
			default-sort-col="name_sortable"
			default-sort-dir="asc"
			:cookie-names="{ genres: 'seriesSelectedGenres', sortCol: 'seriesSortCol', sortDir: 'seriesSortDir' }"
			@apply-filters="applyFilters"
		/>
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