<x-movies-layout main_bg_style="background-image: linear-gradient( rgba(255,255,255,0.5), rgba(255,255,255,0.5)), url(https://image.tmdb.org/t/p/original{{ $movie->backdrop_path }})">
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
					<p class="text-sm mb-2">{{ $movie->genres->pluck('name')->join(' | ') }}</p>
					@if ( is_null($movie->purchase_date) )<p class="text-sm font-normal text-[#3e4b62]">wishlist</p>@endif
					@foreach ($movie->media_types_display as $parent => $media_types)
						<p class="text-sm"><strong>{{ $parent }}</strong>: {{ implode(' | ', $media_types) }}</p>
					@endforeach
					@if ( ! is_null( $movie->collection_id) )
						<p><a href="{{ route('movieCollection.show', $movie->collection ) }}">{{ $movie->collection->name }}</a></p>
					@endif
				</div>
				@auth
					<div class="place-content-center"><a href="{{ route('movies.edit', $movie) }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-300 bg-[#3e4b62] border border-gray-300 leading-5 rounded-md hover:bg-gray-800 hover:text-white focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 transition ease-in-out duration-150">Edit</a></div>
				@endauth
			</div>
			<p class="mt-4">{{ $movie->overview }}</p>
			<div class="mt-4" x-data="{ showAll: false, castToShow: 20 }">
				<div class="flex items-center justify-between">
					<h2 class="text-2xl">Cast Members</h2>
					<button @click="showAll = !showAll" class="relative cursor-pointer px-4 py-2 text-sm font-medium text-gray-300 bg-[#3e4b62] border border-gray-300 leading-5 rounded-md hover:bg-gray-800 hover:text-white focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 transition ease-in-out duration-150">
						<span x-text="showAll ? 'Show Less' : 'Show All'"></span>
					</button>
				</div>
				<ul class="mt-4 columns-1 gap-4 lg:columns-2">
					@foreach ($movie->cast_members as $index => $cast_member)
						@continue( str_contains( $cast_member->pivot->character, 'uncredited' ) )
						<li class="flex w-full" x-show="showAll || {{ $index }} < castToShow">
							{{ $cast_member->name }}
							<span class="text-right flex flex-1 before:border-b-gray-500 before:border-b-2 before:border-dotted before:content-[''] before:flex-1 before:mx-1 before:mb-[0.3rem]">{{ $cast_member->pivot->character }}</span>
						</li>
					@endforeach
				</ul>
			</div>
		</div>
	</div>
	<div class="grid grid-cols-[repeat(auto-fill,minmax(154px,1fr))] place-items-center gap-5 mt-6 text-gray-900">
		<p class="bg-white/80 p-2 rounded-xl col-span-full text-2xl font-bold justify-self-start w-full">Recommendations</p>
		@foreach ($recommendations as $movie)
			@if ($owned_recommendations->contains($movie->id))
				@include('movies.partials.select-movie-link', [ 'movie' => $owned_recommendations->find($movie->id), 'size' => 'w154'])
			@else
				@include('movies.partials.select-movie-form', [ 'size' => 'w154' ])
			@endif
		@endforeach
	</div>
</x-movies-layout>