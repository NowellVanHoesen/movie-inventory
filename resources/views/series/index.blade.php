<x-series-layout>
	<x-slot:heading>Series</x-slot:heading>
	<div class="grid grid-cols-[repeat(auto-fill,minmax(185px,1fr))] place-items-center gap-5 mt-6">
		@each('series.partials.select-series-link', $series, 'series')
	</div>
	<div class="mt-4">{{ $series->links() }}</div>
</x-series-layout>