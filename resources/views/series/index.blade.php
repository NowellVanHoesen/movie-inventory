<x-series-layout page_title="{{ $page_title ?? 'Movie Inventory - Series' }}">
	<div class="grid grid-cols-[repeat(auto-fill,minmax(156px,1fr))] place-items-center gap-1 sm:gap-6 mt-6">
		@each('series.partials.select-series-link', $series, 'series')
	</div>
	<div class="mt-4">{{ $series->links() }}</div>
</x-series-layout>