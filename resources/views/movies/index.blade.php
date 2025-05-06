<x-movies-layout>
	<x-slot:heading>Movies</x-slot:heading>

	<div class="grid grid-cols-[repeat(auto-fill,minmax(185px,1fr))] place-items-center gap-5 mt-6">
		@each('movies.partials.select-movie-link', $movies, 'movie')
	</div>
	<div class="mt-4">{{ $movies->links() }}</div>
</x-movies-layout>