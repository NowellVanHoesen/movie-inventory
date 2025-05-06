<div class="grid grid-cols-[repeat(auto-fill,minmax(185px,1fr))] gap-5 mt-6">
	@foreach ($search_results->results as $movie)
		@if ($local_results->contains($movie->id))
			@include('movies.partials.select-movie-link', [ 'movie' => $local_results->find($movie->id)])
		@else
			@include('movies.partials.select-movie-form')
		@endif
	@endforeach

</div>
