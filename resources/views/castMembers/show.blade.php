<x-castmember-layout page_title="{{ $pageTitle ?? 'Cast Member' }}" name="{{ $castMember->name }}">
	<div class="text-white-900 mt-6 p-6 rounded-xl">
		<div class="md:grid md:grid-cols-[185px_1fr] gap-4">
			<div>
				@if ($castMember->profile_path)
					<img src={{ "https://image.tmdb.org/t/p/w185/" . $castMember->profile_path }} alt="{{ $castMember->name }} profile picture" class="rounded-xl">
				@else
					<img src="{{config('tmdb.placeholder.poster')}}" class="border border-gray-900 rounded-xl max-w-[185]" />
				@endif
			</div>
			<div>
				@if( $memberMovies->isNotEmpty() )
					<p class="p-2 rounded-xl col-span-full text-2xl font-bold justify-self-start w-full">Movies in Inventory</p>
					<div class="grid grid-cols-[repeat(auto-fill,minmax(154px,1fr))] place-items-top gap-1 sm:gap-2 mt-4">
						@foreach ($memberMovies as $movie)
							<span>@include('movies.partials.select-movie-link', [ 'movie' => $memberMovies->find($movie->id) ]){{$movie->pivot->character}}</span>
						@endforeach
					</div>
				@endif
				@if( $memberSeries->isNotEmpty() )
					<p class="p-2 rounded-xl col-span-full text-2xl font-bold justify-self-start w-full mt-6">Series in Inventory</p>
					<div class="grid grid-cols-[repeat(auto-fill,minmax(154px,1fr))] place-items-top gap-1 sm:gap-2 mt-4">
						@each('series.partials.select-series-link', $memberSeries, 'series')
					</div>
				@endif
			</div>
		</div>
	</div>
</x-castmember-layout>
