@props([
	'size' => 'w154'
])

<x-poster-link href="{{ route('movies.show', $movie) }}" id="{{ $movie->slug }}">
	<x-item-status-icon :purchased="! is_null( $movie->purchase_date )" />
	<x-item-poster :placeholder="is_null( $movie->poster_path )" poster_path="{{ $movie->poster_path }}" size="{{ $size }}" />
	<x-poster-overlay-detail
		class="{{ is_null( $movie->poster_path ) ? '' : 'invisible group-hover:visible' }} text-left"
		title="{{ $movie->title }}"
		release_year="{{ $movie->release_date ? date( 'Y', strtotime( $movie->release_date )) : 'TBA' }}"
	/>
</x-poster-link>
