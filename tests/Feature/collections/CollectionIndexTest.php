<?php

use Database\Seeders\MoviesSeeder;
use function Pest\Laravel\get;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('displays a collection page', function () {
	get(route('movieCollection.index'))
    	->assertOk();
});

it('displays posters for collections for movies added to the app', function() {
    $this->seed(MoviesSeeder::class);

	get(route('movieCollection.index'))
		->assertOk()
		// This page is rendered client-side by Vue from Inertia props (no SSR), so
		// collection names and poster paths only show up in the initial page's
		// embedded JSON, not as visible text or <img> tags in the raw HTML response.
		// assertSee checks the raw response body (unlike assertSeeText, which strips
		// tags/attributes and would miss data embedded in the data-page attribute).
		->assertSee([
			'Harry Potter Collection',
			'Fallen Collection',
			'John Wick Collection',
		])
		// Slashes come back JSON-escaped (\/) inside the embedded page data, so
		// match on the filename/hash alone rather than the leading path slash.
		->assertSee([
			'dummy_200x300_ffffff_3e4b62_poster-not-provided.png',
			'yVO4Py2gZ2yFruiscOkEyrrtXFa.jpg',
			'qIm2nHXLpBBdMxi8dvfrnDkBUDh.jpg',
		], false);
});