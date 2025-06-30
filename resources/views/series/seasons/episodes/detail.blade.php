<div class="grid gap-2 lg:grid-cols-[300px_1fr] rounded-lg bg-white/80 mt-2 mb-auto">
	<img src="{{ is_null( $episode->still_path ) ? config('tmdb.placeholder.still') : "https://image.tmdb.org/t/p/w300" . $episode->still_path }}" class="rounded-tl-lg" />
	<div class="p-2">
		<a href="{{ route( 'season.show', [$series,$season] ) }}" class="text-sm text-blue-600 md:float-end px-2">Episode List</a>
		<p class="text-lg">
			<span class="text-2xl">{{ $series->name }}</span>: {{ $season->name }},
			<br />{{ $episode->name }}
		</p>
		<p class="text-sm mt-2">
			<strong>First Air Date</strong>: {{ $episode->air_date }}
		</p>
		<p class="text-sm">
			<strong>Runtime</strong>: {{ $episode->runtime }} min.
		</p>
		<p></p>
	</div>
	<p class="mt-4 lg:col-span-2 pb-2 px-3">{{ $episode->overview }}</p>
</div>
