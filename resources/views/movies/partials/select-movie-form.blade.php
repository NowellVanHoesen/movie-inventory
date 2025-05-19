<form method="POST" action="{{ route('movies.search') }}" class="block relative group border border-gray-900 rounded-xl max-w-[185px]">
	@csrf
	<input type="hidden" name="movie_id" value="{{ $movie->id }}" />
	<button type="submit" class="cursor-pointer p-0" @disabled(empty( $movie->release_date ))>
		@if ( is_null( $movie->poster_path ) )
			<img src={{ env('POSTER_PLACEHOLDER') }} class="rounded-xl max-w-full" />
		@else
			<img src={{ "https://image.tmdb.org/t/p/w185" . $movie->poster_path }} class="rounded-xl max-w-full" />
		@endif
		<div class="text-gray-900 p-2 absolute bottom-0 left-0 right-0 bg-white/75 rounded-b-xl invisible group-hover:visible">
			<p class="font-bold">{{ $movie->title }}</p>
			<p class="text-sm">{{ $movie->release_date ? date( 'Y', strtotime( $movie->release_date )) : "TBA" }}</p>
		</div>
	</button>
</form>
