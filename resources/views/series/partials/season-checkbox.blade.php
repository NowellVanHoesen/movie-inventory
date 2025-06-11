<label htmlFor="season_number_{{ $season->season_number }}" class="grid gap-2 grid-cols-[94px_minmax(106px,1fr)] border rounded-lg max-w-75 bg-white/70 has-checked:shadow-lg has-checked:shadow-blue-500">
	@if ( is_null( $season->poster_path ) )
		<img src={{ config('tmdb.placeholder.poster') }} class="rounded-l-lg max-w-23" />
	@else
		<img src={{ "https://image.tmdb.org/t/p/w92" . $season->poster_path }} class="rounded-l-lg max-w-23" />
	@endif
	<div class="p-2">
		<p>
			{{ $series_detail->name }}: {{ $season->name }}
			@if ( $season->air_date )
				({{ date( 'Y', strtotime( $season->air_date ) ) }})
			@endif
		</p>
		<p>Episodes: {{ $season->episode_count }}</p>
		<input id="season_number_{{ $season->season_number }}" type="checkbox" class="hidden" name="season_numbers[]" value="{{ $season->season_number }}" />
	</div>
</label>