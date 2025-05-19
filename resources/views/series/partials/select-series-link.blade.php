<a href="{{ route( 'series.show', [$series] ) }}" class="block relative group border border-gray-900 rounded-xl max-w-[185px]">
	@if ( is_null( $series->purchase_date ) )
		<i class="absolute top-4 right-2 fa-solid fa-heart fa-lg text-shadow-lg/30 text-[#f5131a]"></i>
	@else
		<i class="absolute top-4 right-2 fa-solid fa-circle-check fa-lg text-shadow-lg/30 text-[#bada55]"></i>
	@endif
	@if ( is_null( $series->poster_path ) )
		<img src={{ env('POSTER_PLACEHOLDER') }} class="rounded-xl max-w-full" />
	@else
		<img src={{ "https://image.tmdb.org/t/p/w185" . $series->poster_path }} class="rounded-xl max-w-full" />
	@endif
	<div class="text-gray-900 p-2 absolute bottom-0 left-0 right-0 bg-white/75 rounded-b-xl invisible group-hover:visible">
		<p class="font-bold">{{ $series->name }}</p>
		<p class="text-sm">{{ date( 'Y', strtotime( $series->first_air_date ) ) }} ({{ $series->certification->name }})</p>
	</div>
</a>