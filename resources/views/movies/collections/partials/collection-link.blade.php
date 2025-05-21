<a href="/movieCollection/{{ $collection->id }}" class="block relative group border border-gray-900 rounded-xl max-w-[190px]">
	@if ( is_null( $collection->poster_path ) )
		<img src={{ env('POSTER_PLACEHOLDER') }} class="rounded-xl max-w-full" />
	@else
		<img src={{ "https://image.tmdb.org/t/p/w200" . $collection->poster_path }} class="rounded-xl max-w-full" />
	@endif
	<div class="text-gray-900 p-2 absolute bottom-0 left-0 right-0 bg-white/60 rounded-b-xl invisible group-hover:visible">
		<span class="font-bold">{{ $collection->name }}</span><br>
	</div>
</a>