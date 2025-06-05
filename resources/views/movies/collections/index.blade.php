<x-movies-layout>
	<div class="grid grid-cols-[repeat(auto-fill,minmax(185px,1fr))] place-items-center gap-5 mt-6">
		@each ('movies.collections.partials.collection-link', $collections, 'collection')
	</div>
	<div class="mt-4">{{ $collections->links() }}</div>
</x-movies-layout>