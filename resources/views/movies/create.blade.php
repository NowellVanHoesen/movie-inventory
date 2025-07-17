@props(['search_results' => [], 'movie' => null])

<x-movies-layout page_title="{{ $page_title ?? 'Movie Inventory' }}">
	@include('movies.partials.search')

	@if ($search_results)
		@include('movies.partials.search-results')
	@elseif ($movie)
		@include('movies.partials.detail')
	@endif
</x-movies-layout>
