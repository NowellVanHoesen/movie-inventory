<a href="{{ route( 'series.show', [$series] ) }}" class="block relative border border-gray-900 rounded-xl max-w-[185px]">
	@if ( is_null( $series->poster_path ) )
		<img src={{ env('POSTER_PLACEHOLDER') }} class="rounded-xl max-w-full" />
	@else
		<img src={{ "https://image.tmdb.org/t/p/w185" . $series->poster_path }} class="rounded-xl max-w-full" />
	@endif
	<div class="text-gray-900 p-2 absolute bottom-0 left-0 right-0 bg-white/60 rounded-b-xl">
		<span class="font-bold">{{ $series->name }}</span><br>
		<div class="text-sm flex flex-row items-center justify-between">
			<div>{{ date( 'Y', strtotime( $series->first_air_date ) ) }} ({{ $series->certification->name }})</div>
			@if ( is_null( $series->purchase_date ) )
				<i class="fa-solid fa-heart fa-lg text-shadow-lg/30 text-[#f5131a]"></i>
			@else
				<i class="fa-solid fa-circle-check fa-lg text-shadow-lg/30 text-[#bada55]"></i>
			@endif
		</div>
	</div>
</a>