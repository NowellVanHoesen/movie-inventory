<x-series-layout>
	<div class="grid grid-cols-[repeat(auto-fill,minmax(154px,1fr))] place-items-center gap-1 md:gap-2 mt-6">
		@each('series.partials.select-series-link', $series, 'series')
	</div>
	<div class="mt-4">{{ $series->links() }}</div>
</x-series-layout>