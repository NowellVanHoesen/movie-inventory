<x-poster-link href="{{ route( 'series.show', [$series] ) }}" id="{{ $series->id }}">
	<x-item-status-icon :purchased="! is_null( $series->purchase_date )" />
	<x-item-poster :placeholder="is_null( $series->poster_path )" poster_path="{{ $series->poster_path }}" size="w185" />
	<x-poster-overlay-detail
		class="{{ is_null( $series->poster_path ) ? '' : 'invisible group-hover:visible' }} text-left"
		title="{{ $series->name }}"
		release_year="{{ $series->first_air_date ? date( 'Y-m-d', strtotime( $series->first_air_date )) : 'TBA' }}"
		certification="({{ $series->certification->name }})"
	/>
</x-poster-link>
