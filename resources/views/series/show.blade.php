<x-series-layout main_bg_style="background-image: linear-gradient( rgba(255,255,255,0.5), rgba(255,255,255,0.5)), url(https://image.tmdb.org/t/p/original{{ $series->backdrop_path }})">
	<x-slot:heading>
		Series: {{ $series->name }}
	</x-slot:heading>
	<x-slot:tagline>
		{{ $series->tagline }}
	</x-slot:tagline>
	<div class="grid grid-cols-[1fr_calc(50%-93px)] gap-4 text-gray-900">
		@include( 'series.partials.detail' )
		{{-- @include( 'series.seasons.detail' ) --}}
		<div class="bg-white/80 mt-6 p-6 rounded-xl row-span-2 mb-auto">
			<x-cast_members :cast="$series->cast_members" />
		</div>
		<p class="text-2xl font-bold">Seasons</p>
		<div class="flex flex-none flex-wrap gap-4 justify-between">
			@foreach ( $series->seasons as $season )
				<a href="{{ route('season.show', [$series, $season]) }}" class="group relative rounded-lg">
					<img src="{{ is_null( $season->poster_path ) ? env('POSTER_PLACEHOLDER') : "https://image.tmdb.org/t/p/w92" . $season->poster_path }}" class="rounded-lg border" />
					<div class="p-2 leading-none absolute bottom-0 left-0 right-0 bg-white/75 rounded-b-lg invisible group-hover:visible">
						<p>{{ $season->name }}</p>
						<p>Ep: {{ count( $season->episodes ) }}</p>
						@if ( ! is_null( $season->air_date ) )
							<p>({{ date( 'Y', strtotime( $season->air_date ) ) }})</p>
						@endif
					</div>
				</a>
			@endforeach
		</div>
	</div>
</x-series-layout>