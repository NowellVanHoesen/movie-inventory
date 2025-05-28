<x-poster-button action="{{ route('movies.create') }}">
	<x-slot:hiddenInputs>
		<x-form-hidden-input name="movie_id" value="{{ $movie->id }}" />
		<x-form-hidden-input name="search_term" value="{{ $search_term }}" />
	</x-slot:hiddenInputs>
	<x-slot:content>
		<x-item-poster :placeholder="is_null( $movie->poster_path )" poster_path="{{ $movie->poster_path }}" size="w185" />
		<x-poster-overlay-detail
			class="{{ is_null( $movie->poster_path ) ? '' : 'invisible group-hover:visible' }} text-left"
			title="{{ $movie->title }}"
			release_year="{{ $movie->release_date ? date( 'Y', strtotime( $movie->release_date )) : 'TBA' }}"
		/>
	</x-slot:content>
</x-poster-button>
