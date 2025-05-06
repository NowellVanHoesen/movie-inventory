<div class="grid grid-cols-[repeat(auto-fill,minmax(185px,1fr))] gap-5 mt-6">
	@foreach ($search_results->results as $series)
		@if ($local_results->contains($series->id))
			@include('series.partials.select-series-link', [ 'series' => $local_results->find($series->id)])
		@else
			@include('series.partials.select-series-form')
		@endif
	@endforeach

</div>
