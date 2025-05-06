<x-series-layout>
    <x-slot:heading>Series</x-slot:heading>
	@include('series.partials.search')

	@if ( ! empty( $search_results ) )
		@include('series.partials.search-results')
	@elseif ( ! empty( $series_detail ) )
		@include('series.partials.form')
	@endif
</x-series-layout>
