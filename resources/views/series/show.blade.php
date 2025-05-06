<x-series-layout main_bg_style="background-image: linear-gradient( rgba(255,255,255,0.8), rgba(255,255,255,0.8)), url(https://image.tmdb.org/t/p/original{{ $series->backdrop_path }})">
	<x-slot:heading>
		Series: {{ $series->name }}
	</x-slot:heading>
	<x-slot:tagline>
		{{ $series->tagline }}
	</x-slot:tagline>
	<div class="grid grid-cols-[1fr_calc(50%-93px)] gap-4 text-gray-900">
		@include( 'series.partials.detail' )
		{{-- @include( 'series.seasons.detail' ) --}}
		<div class="bg-white/75 mt-6 p-6 rounded-xl row-span-2 mb-auto">
			<x-cast_members :cast="$series->cast_members" />
		</div>
		<div class="grid grid-cols-2 gap-4">
			<p class="col-span-2">Seasons</p>
			@foreach ( $series->seasons as $season )
				<a href="{{ route('season.show', [$series, $season]) }}" class="grid gap-2 grid-cols-[94px_minmax(106px,1fr)] border rounded-lg max-w-75 bg-white/70 mb-auto">
					<img src="{{ is_null( $season->poster_path ) ? env('POSTER_PLACEHOLDER') : "https://image.tmdb.org/t/p/w92" . $season->poster_path }}" class="rounded-l-lg max-w-23" />
					<div class="p-2">
						<p>{{ $series->name }}: {{ $season->name }} ({{ date( 'Y', strtotime( $season->air_date ) ) }})</p>
						<p>Episodes: {{ count( $season->episodes ) }}</p>
					</div>
				</a>
			@endforeach
		</div>
	</div>
</x-series-layout>