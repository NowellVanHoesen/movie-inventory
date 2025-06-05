<x-series-layout main_bg_style="background-image: linear-gradient( rgba(255,255,255,0.5), rgba(255,255,255,0.5)), url(https://image.tmdb.org/t/p/original{{ $series->backdrop_path }})">
	<div class="grid grid-cols-[1fr_calc(50%-93px)] gap-4 text-gray-900">
		<div>
			@include( 'series.partials.detail', ['series' => $series] )
			@include( 'series.seasons.detail' )
			@include( 'series.seasons.episodes.detail' )
		</div>
		<div class="bg-white/80 mt-6 p-6 rounded-xl row-span-3 mb-auto">
			<x-cast_members :cast="$series->cast_members->merge($season->cast_members->merge($episode->cast_members))" />
		</div>
	</div>
</x-series-layout>