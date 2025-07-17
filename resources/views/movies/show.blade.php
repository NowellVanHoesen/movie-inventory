<x-movies-layout page_title="{{ $page_title ?? 'Movie Inventory' }}" main_bg_style="background-image: linear-gradient( rgba(255,255,255,0.5), rgba(255,255,255,0.5)), url(https://image.tmdb.org/t/p/original{{ $movie->backdrop_path }})">
	<div class="bg-white/80 text-gray-900 mt-6 p-6 rounded-xl md:grid md:grid-cols-[185px_1fr] gap-4">
		<div>
			<img src={{ "https://image.tmdb.org/t/p/w185/" . $movie->poster_path }} alt="{{ $movie->title }} movie poster">
		</div>
		<div>
			<div class="sm:flex sm:justify-between sm:items-start">
				<div>
					<h2 class="text-2xl">
						{{ $movie->title }}
						<span class="text-sm font-normal">( {{ $movie->certification->name }} ) {{ $movie->runtime }} min.</span>
					</h2>
					<p><em>{{ $movie->tagline }}</em></p>
					<p class="text-sm">{{ $movie->genres->pluck('name')->join(' | ') }}</p>
					@if ( is_null($movie->purchase_date) )<p class="text-sm font-normal text-[#3e4b62]">wishlist</p>@endif
					@foreach ($movie->media_types_display as $parent => $media_types)
						<p class="text-sm mt-2"><strong>{{ $parent }}</strong>: {{ implode(' | ', $media_types) }}</p>
					@endforeach
					@if ( ! is_null( $movie->collection_id) )
						<p class="mt-2"><a href="{{ route('movieCollection.show', $movie->collection ) }}" class="text-blue-600 hover:underline focus:underline">{{ $movie->collection->name }}</a></p>
					@endif
				</div>
				@auth
					<div class="flex items-center gap-8">
						<div class="place-content-center"><a href="{{ route('movies.edit', $movie) }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-[#3e4b62] border border-transparent rounded-md hover:bg-[#333c50] focus:bg-[#333c50] focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 transition ease-in-out duration-150">Edit</a></div>
						<x-danger-button form="delete-movie" onclick="event.preventDefault(); if (confirm('Are you sure you want to delete this movie?')) { document.getElementById('delete-movie').submit(); }">
							{{ __('Delete') }}
						</x-danger-button>
					</div>
					<form method="POST" action="{{ route('movies.destroy', $movie) }}" id="delete-movie" class="hidden">
						@csrf
						@method('DELETE')
					</form>
				@endauth
			</div>
			<p class="mt-4">{{ $movie->overview }}</p>
			<div class="mt-4" x-data="{ showAll: false, castToShow: 20 }">
				<x-cast_members :cast="$movie->cast_members" display_limit="20" multi_cols="true" />
			</div>
		</div>
	</div>
	@if (count($recommendations))
	<div class="grid grid-cols-[repeat(auto-fill,minmax(154px,1fr))] place-items-center gap-5 mt-6 text-gray-900">
		<p class="bg-white/80 p-2 rounded-xl col-span-full text-2xl font-bold justify-self-start w-full">Recommendations</p>
		@foreach ($recommendations as $movie)
			@if ($owned_recommendations->contains($movie->id))
				@include('movies.partials.select-movie-link', [ 'movie' => $owned_recommendations->find($movie->id) ])
			@else
				@include('movies.partials.select-movie-form')
			@endif
		@endforeach
	</div>
	@endif
</x-movies-layout>