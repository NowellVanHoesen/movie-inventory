<x-layout page_title="{{ $page_title ?? 'Movie Inventory' }}">
	<x-slot:heading>{{ $page_title }}</x-slot:heading>

	<h2 class="text-2xl">Movies</h2>
	<div class="grid grid-cols-[repeat(auto-fill,minmax(154px,1fr))] place-items-center gap-5 mt-2">
		@if ( ! count($movies_results) )
			<p>No Results Found</p>
		@endif
		@each('movies.partials.select-movie-link', $movies_results, 'movie')
	</div>
	<h2 class="text-2xl mt-6">Movie Collections</h2>
	<div class="grid grid-cols-[repeat(auto-fill,minmax(154px,1fr))] place-items-center gap-5 mt-2">
		@if ( ! count($collections_results) )
			<p>No Results Found</p>
		@endif
		@each('movies.collections.partials.collection-link', $collections_results, 'collection')
	</div>
	<h2 class="text-2xl mt-6">Series</h2>
	<div class="grid grid-cols-[repeat(auto-fill,minmax(154px,1fr))] place-items-center gap-5 mt-2">
		@if ( ! count($series_results) )
			<p>No Results Found</p>
		@endif
		@each('series.partials.select-series-link', $series_results, 'series')
	</div>
</x-layout>