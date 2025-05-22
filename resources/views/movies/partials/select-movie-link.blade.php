<a href="/movies/{{ $movie->imdb_id }}" id="{{ $movie->imdb_id }}" class="block group relative border border-gray-900 rounded-xl max-w-[185px]">
	@if ( is_null( $movie->purchase_date ) )
		<i class="absolute top-4 right-2 fa-solid fa-heart fa-lg text-shadow-lg/30 text-[#f5131a]"></i>
	@else
		<i class="absolute top-4 right-2 fa-solid fa-circle-check fa-lg text-shadow-lg/30 text-[#bada55]"></i>
	@endif
</a>	<x-item-poster :placeholder="is_null( $movie->poster_path )" poster_path="{{ $movie->poster_path }}" size="w185" />
	<x-poster-overlay-detail
		class="{{ is_null( $movie->poster_path ) ? '' : 'invisible group-hover:visible' }} text-left"
		title="{{ $movie->title }}"
		release_year="{{ date( 'Y', strtotime( $movie->release_date )) }}"
		certification="({{ $movie->certification->name }})"
	/>
