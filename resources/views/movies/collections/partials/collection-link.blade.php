<a href="{{ route('movieCollection.show', $collection ) }}" class="block relative group border border-gray-900 rounded-xl max-w-[190px]">
	<x-item-poster :placeholder="is_null( $collection->poster_path )" poster_path="{{ $collection->poster_path }}" size="w200" />
	<x-poster-overlay-detail
		class="{{ is_null( $collection->poster_path ) ? '' : 'invisible group-hover:visible' }} text-left"
		title="{{ $collection->name }}"
	/>
</a>