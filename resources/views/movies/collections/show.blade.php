<x-movies-layout page_title="{{ $page_title ?? 'Movie Inventory' }}" main_bg_style="background-image: linear-gradient( rgba(255,255,255,0.5), rgba(255,255,255,0.5)), url(https://image.tmdb.org/t/p/original{{ $collection->backdrop_path }})">
	<div class="bg-white/80 text-gray-900 mt-6 p-6 rounded-xl md:grid md:grid-cols-[300px_1fr] gap-4">
		<div>
			@if ( is_null( $collection->poster_path ) )
				<img src={{ config('tmdb.placeholder.poster') }} class="rounded-xl max-w-full" alt="Movie collection poster placeholder" />
			@else
				<img src={{ "https://image.tmdb.org/t/p/w300" . $collection->poster_path }} class="rounded-xl max-w-full" alt="{{ $collection->name }} movie poster" />
			@endif
		</div>
		<div>
			<h2 class="text-2xl">{{ $collection->name }}</h2>
			<p class="mt-4">{{ $collection->overview }}</p>
			<div class="grid grid-cols-[repeat(auto-fill,minmax(92px,1fr))] gap-3 mt-6 leading-none">
				@foreach ($collection_details->parts as $movie)
					@if ($collection->movies->contains($movie->id))
						@include('movies.partials.select-movie-link', [ 'movie' => $collection->movies->find($movie->id), 'size' => 'w92'])
					@else
						@include('movies.partials.select-movie-form', ['size' => 'w92'])
					@endif
				@endforeach
			</div>
		</div>
	</div>
</x-movies-layout>