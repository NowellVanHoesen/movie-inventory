<x-poster-link href="{{ route( 'movies.show', [$movie] ) }}" id="{{ $movie->imdb_id }}">
	<x-item-status-icon :purchased="! is_null( $movie->purchase_date )" />
	<x-item-poster :placeholder="is_null( $movie->poster_path )" poster_path="{{ $movie->poster_path }}" size="w185" />
	<x-poster-overlay-detail
		class="{{ is_null( $movie->poster_path ) ? '' : 'invisible group-hover:visible' }} text-left"
		title="{{ $movie->title }}"
		release_year="{{ date( 'Y', strtotime( $movie->release_date )) }}"
		certification="({{ $movie->certification->name }})"
	/>
</x-poster-link>
