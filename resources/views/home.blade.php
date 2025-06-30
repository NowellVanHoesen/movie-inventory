<x-layout>
	<x-slot:heading>Home Page</x-slot:heading>

	<h2 class="text-2xl">Latest Movies</h2>
	<div class="grid grid-cols-[repeat(auto-fill,minmax(154px,1fr))] place-items-center gap-2 mt-2">
		@each('movies.partials.select-movie-link', $movies, 'movie')
	</div>

	<h2 class="text-2xl mt-6">Latest Series</h2>
	<div class="grid grid-cols-[repeat(auto-fill,minmax(154px,1fr))] place-items-center gap-2 mt-2">
		@each('series.partials.select-series-link', $series, 'series')
	</div>
</x-layout>