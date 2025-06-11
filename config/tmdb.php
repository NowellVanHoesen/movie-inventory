<?php

return [

	'api' => [
		'auth_key' => env( 'TMDB_API_AUTH_KEY', '' ),
		'base_url' => env( 'TMDB_API_BASE_URL', 'https://api.themoviedb.org' ),
		'version' => env( 'TMDB_API_VERSION', '3' ),
	],

	'base_url' => [
		'movie' => env( 'IMDB_MOVIE_BASE_URL', 'https://imdb.com/title' ),
		'show' => env( 'IMDB_SHOW_BASE_URL', 'https://imdb.com/title' ),
		'people' => env( 'IMDB_PEOPLE_BASE_URL', 'https://imdb.com/name' ),
	],

	'placeholder' => [
		'poster' => env( 'POSTER_PLACEHOLDER', '/images/dummy_200x300_ffffff_3e4b62_poster-not-provided.png' ),
		'still' => env( 'STILL_PLACEHOLDER', '/images/dummy_185x102_ffffff_3e4b62_still-not-provided.png' ),
	],
];