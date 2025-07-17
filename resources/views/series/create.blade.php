<x-series-layout page_title="{{ $page_title ?? 'Movie Inventory - Series' }}">
	@include('series.partials.search')
	@if ( ! empty( $search_results ) )
		@include('series.partials.search-results')
	@elseif ( ! empty( $series_detail ) )
		@include('series.partials.form')
	@endif
</x-series-layout>
