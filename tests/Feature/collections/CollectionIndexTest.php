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
		->assertSeeText([
			'Harry Potter Collection',
			'Fallen Collection',
			'John Wick Collection',
		])
		->assertSeeHtml([
			'src="/images/dummy_200x300_ffffff_3e4b62_poster-not-provided.png"',
			'src="https://image.tmdb.org/t/p/w154/yVO4Py2gZ2yFruiscOkEyrrtXFa.jpg"',
			'src="https://image.tmdb.org/t/p/w154/qIm2nHXLpBBdMxi8dvfrnDkBUDh.jpg"',
		], false);
});