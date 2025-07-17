@php
	$title_sort = 'title';
	$release_date_sort = 'release_date';
	$purchase_date_sort = 'purchase_date';

	if ( ! is_null(request()->input('sort')) ) {
		if ( 'title' ===request()->input('sort') ) {
			$title_sort = 'title|desc';
		} else if ( 'release_date' ===request()->input('sort') ) {
			$release_date_sort = 'release_date|desc';
		} else if ( 'purchase_date' ===request()->input('sort') ) {
			$purchase_date_sort = 'purchase_date|desc';
		}
	}

	$params = [];

	if ( request()->has('wishlist') ) {
		$params[] = 'wishlist';
	} elseif ( request()->has('purchased') ) {
		$params[] = 'purchased';
	}

	array_flip($params);

	$req_query = request()->query();
@endphp

<x-movies-layout page_title="{{ $page_title ?? 'Movie Inventory' }}">
	<div class="md:space-x-4 space-x-0 flex justify-start md:items-center md:justify-end flex-wrap md:flex-nowrap">
		<strong class="basis-full md:basis-auto">Sort:</strong>
		<x-nav-link href="{{ route(Route::currentRouteName(), array_merge($params, ['sort' => $title_sort])) }}">{{ $title_sort === 'title' ? 'Title A - Z' : 'Title Z - A' }}</x-nav-link>
		<x-nav-link href="{{ route(Route::currentRouteName(), array_merge($params, ['sort' => $release_date_sort])) }}">{{ $release_date_sort === 'release_date' ? 'Release Date' : 'Release Date desc' }}</x-nav-link>
		<x-nav-link href="{{ route(Route::currentRouteName(), array_merge($params, ['sort' => $purchase_date_sort])) }}">{{ $purchase_date_sort === 'purchase_date' ? 'Purchase Date' : 'Purchase Date desc' }}</x-nav-link>
	</div>
	<div class="grid grid-cols-[repeat(auto-fill,minmax(154px,1fr))] place-items-center gap-1 sm:gap-2 mt-6">
		@each('movies.partials.select-movie-link', $movies, 'movie')
	</div>
	<div class="mt-4">{{ $movies->appends(array_merge(array_flip($params), ['sort' => request()->input('sort')]))->links() }}</div>
</x-movies-layout>