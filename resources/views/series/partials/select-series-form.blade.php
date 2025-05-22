<x-poster-button action="{{ route('series.create') }}">
	<x-slot:hiddenInputs>
		<x-form-hidden-input name="series_id" value="{{ $series->id }}" />
		<x-form-hidden-input name="search_term" value="{{ $search_term }}" />
	</x-slot:hiddenInputs>
	<x-slot:content>
		<x-item-poster :placeholder="is_null( $series->poster_path )" poster_path="{{ $series->poster_path }}" size="w185" />
		<x-poster-overlay-detail
			class="{{ is_null( $series->poster_path ) ? '' : 'invisible group-hover:visible' }} text-left"
			title="{{ $series->name }}"
			release_year="{{ $series->first_air_date ? date( 'Y-m-d', strtotime( $series->first_air_date )) : 'TBA' }}"
		/>
	</x-slot:content>
</x-poster-button>
