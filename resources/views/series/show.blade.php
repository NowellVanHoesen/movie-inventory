<x-series-layout main_bg_style="background-image: linear-gradient( rgba(255,255,255,0.5), rgba(255,255,255,0.5)), url(https://image.tmdb.org/t/p/original{{ $series->backdrop_path }})">
	<div class="grid grid-cols-1 lg:grid-cols-[1fr_calc(50%-93px)] gap-4 text-gray-900">
		@include( 'series.partials.detail' )
		{{-- @include( 'series.seasons.detail' ) --}}
		<div class="bg-white/80 lg:mt-6 mt-2 p-6 rounded-xl row-span-2 mb-auto">
			<x-cast_members :cast="$series->cast_members" />
		</div>
		<p class="bg-white/80 p-2 rounded-xl text-2xl font-bold">Seasons</p>
		<div class="flex flex-none flex-wrap gap-4">
			@foreach ( $series->seasons as $season )
				<a href="{{ route('season.show', [$series, $season]) }}" class="group relative rounded-lg">
					<img src="{{ is_null( $season->poster_path ) ? config('tmdb.placeholder.poster') : "https://image.tmdb.org/t/p/w92" . $season->poster_path }}" class="rounded-lg border max-w-[92px]" />
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
	@if ( count( $recs ) )
	<div class="grid grid-cols-[repeat(auto-fill,minmax(154px,1fr))] place-items-center gap-5 mt-6 text-gray-900">
		<p class="bg-white/80 p-2 rounded-xl col-span-full text-2xl font-bold justify-self-start w-full">Recommendations</p>
		@foreach ($recs as $series)
			@if ($owned_recs->contains($series->id))
				@include('series.partials.select-series-link', [ 'series' => $owned_recs->find($series->id) ])
			@else
				@include('series.partials.select-series-form')
			@endif
		@endforeach
	</div>
	@endif
</x-series-layout>