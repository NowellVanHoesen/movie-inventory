<a href="{{ route( 'series.show', [$series] ) }}" class="block relative group border border-gray-900 rounded-xl max-w-[185px]">
	@if ( is_null( $series->purchase_date ) )
		<i class="absolute top-4 right-2 fa-solid fa-heart fa-lg text-shadow-lg/30 text-[#f5131a]"></i>
	@else
		<i class="absolute top-4 right-2 fa-solid fa-circle-check fa-lg text-shadow-lg/30 text-[#bada55]"></i>
	@endif
</a>	<x-item-poster :placeholder="is_null( $series->poster_path )" poster_path="{{ $series->poster_path }}" size="w185" />
	<x-poster-overlay-detail
		class="{{ is_null( $series->poster_path ) ? '' : 'invisible group-hover:visible' }} text-left"
		title="{{ $series->name }}"
		release_year="{{ $series->first_air_date ? date( 'Y-m-d', strtotime( $series->first_air_date )) : 'TBA' }}"
		certification="({{ $series->certification->name }})"
	/>
