<form method="POST" action="{{ route('series.create') }}" class="block relative border border-gray-900 rounded-xl max-w-[185px] mb-auto">
	@csrf
	<input type="hidden" name="series_id" value="{{ $series->id }}" />
	<input type="hidden" name="search_term" value="{{ $search_term }}" />
	<button type="submit" class="cursor-pointer p-0">
	</button>
</form>
		<x-item-poster :placeholder="is_null( $series->poster_path )" poster_path="{{ $series->poster_path }}" size="w185" />
		<x-poster-overlay-detail
			class="{{ is_null( $series->poster_path ) ? '' : 'invisible group-hover:visible' }} text-left"
			title="{{ $series->name }}"
			release_year="{{ $series->first_air_date ? date( 'Y-m-d', strtotime( $series->first_air_date )) : 'TBA' }}"
		/>
