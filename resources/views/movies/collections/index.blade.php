<x-movies-layout page_title="{{ $page_title ?? 'Movie Inventory' }}">
	<div class="grid grid-cols-[repeat(auto-fill,minmax(154px,1fr))] place-items-center gap-1 sm:gap-2 mt-6">
		@each ('movies.collections.partials.collection-link', $collections, 'collection')
	</div>
	<div class="mt-4">{{ $collections->links() }}</div>
</x-movies-layout>