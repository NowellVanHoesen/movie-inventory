<div class="grid col-span-2 grid-cols-[repeat(auto-fill,minmax(185px,1fr))]  gap-2 mt-2">
	<p class="col-span-full text-2xl font-bold">Episodes</p>
	@foreach ( $season->episodes as $episode )
		<a class="grid gap-2 justify-items-center border rounded-lg max-w-47 mx-auto bg-white/80" href="{{ route( 'episode.show', [$series, $season, $episode]) }}">
			<img src="{{ empty( $episode->still_path ) ? config('tmdb.placeholder.still') : "https://image.tmdb.org/t/p/w154" . $episode->still_path }}" class="rounded-t-lg max-w-[154px]" />
			<span class="text-sm p-2">#{{ $episode->episode_number }}: {{ $episode->name }}</span>
		</a>
	@endforeach
</div>