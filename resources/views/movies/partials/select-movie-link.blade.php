<a href="/movies/{{ $movie->imdb_id }}" id="{{ $movie->imdb_id }}" class="block group relative border border-gray-900 rounded-xl max-w-[185px]">
	@if ( is_null( $movie->purchase_date ) )
		<i class="absolute top-4 right-2 fa-solid fa-heart fa-lg text-shadow-lg/30 text-[#f5131a]"></i>
	@else
		<i class="absolute top-4 right-2 fa-solid fa-circle-check fa-lg text-shadow-lg/30 text-[#bada55]"></i>
	@endif
	@if ( is_null( $movie->poster_path ) )
		<img src={{ env('POSTER_PLACEHOLDER') }} class="rounded-xl max-w-full" />
	@else
		<img src={{ "https://image.tmdb.org/t/p/w185" . $movie->poster_path }} class="rounded-xl max-w-full" />
	@endif
	<div class="text-gray-900 p-2 absolute bottom-0 left-0 right-0 bg-white/75 rounded-b-xl invisible group-hover:visible">
		<p class="font-bold invisible group-hover:visible">{{ $movie->title }}</p>
		<p class="text-sm">{{ date( 'Y', strtotime( $movie->release_date ) ) }} ({{ $movie->certification->name }})</p>
	</div>
</a>