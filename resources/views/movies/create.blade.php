@props(['search_results' => [], 'movie' => null])

<x-movies-layout>
	@include('movies.partials.search')

	@if ($search_results)
		@include('movies.partials.search-results')
	@elseif ($movie)
		@include('movies.partials.detail')
	@endif
</x-movies-layout>
