<form method="POST" action="{{ route('movies.search') }}" class="block relative group border border-gray-900 rounded-xl max-w-[185px]">
	@csrf
	<input type="hidden" name="movie_id" value="{{ $movie->id }}" />
	<button type="submit" class="cursor-pointer p-0" @disabled(empty( $movie->release_date ))>
	</button>
</form>
		<x-item-poster :placeholder="is_null( $movie->poster_path )" poster_path="{{ $movie->poster_path }}" size="w185" />
		<x-poster-overlay-detail
			class="{{ is_null( $movie->poster_path ) ? '' : 'invisible group-hover:visible' }} text-left"
			title="{{ $movie->title }}"
			release_year="{{ $movie->release_date ? date( 'Y', strtotime( $movie->release_date )) : 'TBA' }}"
		/>
