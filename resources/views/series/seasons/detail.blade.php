<div class="grid gap-2 md:grid-cols-[154px_minmax(106px,1fr)] rounded-lg max-w-full bg-white/80 mt-2 mb-auto">
	<img src="{{ is_null( $season->poster_path ) ? config('tmdb.placeholder.poster') : "https://image.tmdb.org/t/p/w154" . $season->poster_path }}" class="rounded-l-lg max-w-39" />
	<div class="p-2">
		<a href="{{ route( 'series.show', [$series] ) }}" class="text-sm text-blue-600 px-2 md:float-end">Season List</a>
		<p class="text-2xl">{{ $series->name }}: <span class="text-lg whitespace-nowrap">{{ $season->name }}</span></p>
		<p class="text-sm"><strong>First AirDate</strong>: {{ $season->air_date }}</p>
		<p class="text-sm"><strong>Episodes</strong>: {{ count( $season->episodes ) }}</p>
		<p class="mt-4">{{ $season->overview }}</p>
		<p></p>
	</div>
</div>
