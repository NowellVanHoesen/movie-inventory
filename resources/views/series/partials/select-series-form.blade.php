<form method="POST" action="{{ route('series.create') }}" class="block relative border border-gray-900 rounded-xl max-w-[185px] mb-auto">
	@csrf
	<input type="hidden" name="series_id" value="{{ $series->id }}" />
	<input type="hidden" name="search_term" value="{{ $search_term }}" />
	<button type="submit" class="cursor-pointer p-0">
		@if ( is_null( $series->poster_path ) )
			<img src={{ env('POSTER_PLACEHOLDER') }} class="rounded-xl max-w-full" />
		@else
			<img src={{ "https://image.tmdb.org/t/p/w185" . $series->poster_path }} class="rounded-xl max-w-full" />
		@endif
		<div class="text-gray-900 p-2 absolute bottom-0 left-0 right-0 bg-white/75 rounded-b-xl invisible group-hover:visible">
			<span class="font-bold">{{ $series->name }}</span><br>
			<div class="text-sm flex flex-row items-center justify-between">
				<div>{{ $series->first_air_date ? date( 'Y-m-d', strtotime( $series->first_air_date )) : "TBA" }}</div>
			</div>
		</div>
	</button>
</form>
