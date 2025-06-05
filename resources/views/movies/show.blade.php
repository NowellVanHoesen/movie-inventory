<x-movies-layout main_bg_style="background-image: linear-gradient( rgba(255,255,255,0.5), rgba(255,255,255,0.5)), url(https://image.tmdb.org/t/p/original{{ $movie->backdrop_path }})">
	<x-slot:heading>
		{{ $movie->title }}
		<span class="text-sm font-normal">( {{ $movie->certification->name }} ) {{ $movie->runtime }} min.</span>
	</x-slot:heading>
	<x-slot:tagline>
		{{ $movie->tagline }}
	</x-slot:tagline>
	<div class="bg-white/80 text-gray-900 mt-6 p-6 rounded-xl md:grid md:grid-cols-[150px_1fr] gap-4">
		<div>
			<img src={{ "https://image.tmdb.org/t/p/w154/" . $movie->poster_path }} alt="{{ $movie->title }} movie poster">
		</div>
		<div>
			<div class="sm:flex sm:justify-between sm:items-start">
				<div>
					<p class="text-sm">{{ $movie->genres->pluck('name')->join(' | ') }}</p>
					@foreach ($movie->media_types_display as $parent => $media_types)
						<p class="text-sm"><strong>{{ $parent }}</strong>: {{ implode(' | ', $media_types) }}</p>
					@endforeach
					@if ( ! is_null( $movie->collection_id) )
						<p><a href="{{ route('movieCollection.show', $movie->collection_id ) }}">{{ $movie->collection->name }}</a></p>
					@endif
				</div>
				@auth
					<div class="place-content-center"><a href="{{ route('movies.edit', [ 'movie' => $movie->imdb_id ] ) }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-300 bg-[#3e4b62] border border-gray-300 leading-5 rounded-md hover:bg-gray-800 hover:text-white focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 transition ease-in-out duration-150">Edit</a></div>
				@endauth
			</div>
			<p class="mt-4">{{ $movie->overview }}</p>
			<ul class="mt-4 columns-1 gap-4 lg:columns-2">
				@foreach ($movie->cast_members as $cast_member)
					@continue( str_contains( $cast_member->pivot->character, 'uncredited' ) )

					<li class="flex w-full">
						{{ $cast_member->name }}
						<span class="text-right flex flex-1 before:border-b-gray-500 before:border-b-2 before:border-dotted before:content-[''] before:flex-1 before:mx-1 before:mb-[0.3rem]">{{ $cast_member->pivot->character }}</span>
					</li>
				@endforeach
			</ul>
		</div>
	</div>
</x-movies-layout>