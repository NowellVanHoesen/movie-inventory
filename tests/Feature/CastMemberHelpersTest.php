<?php

use App\Models\CastMember;
use App\Models\Movie;
use App\Traits\CastMemberHelpers;
use Database\Seeders\MoviesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Direct coverage for App\Traits\CastMemberHelpers, which every ingestion job
 * depends on but nothing tested. It batches the membership check and the pivot
 * insert, so the duplicate/idempotency behaviour it used to get for free from a
 * per-member exists() query now has to be asserted explicitly.
 */
beforeEach(function () {
    $this->seed(MoviesSeeder::class);

    // The trait's methods are private, so reach them through a host class the
    // same way the jobs compose the trait.
    $this->host = new class {
        use CastMemberHelpers;

        public function attach($model, $castMembers): void {
            $this->attachCastMemberToModel($model, $castMembers);
        }
    };
});

function tmdbCredit(int $id, string $character, int $order): object {
    return (object) [
        'id' => $id,
        'name' => "Person {$id}",
        'original_name' => "Person {$id}",
        'profile_path' => null,
        'character' => $character,
        'order' => $order,
    ];
}

it('creates and attaches new cast members with their character and order', function () {
    $movie = Movie::firstOrFail();
    $before = $movie->cast_members()->count();

    $this->host->attach($movie, [
        tmdbCredit(9000001, 'The Lead', 0),
        tmdbCredit(9000002, 'The Foil', 1),
    ]);

    expect($movie->cast_members()->count())->toBe($before + 2);

    $attached = $movie->cast_members()->where('cast_members.id', 9000001)->firstOrFail();

    expect($attached->pivot->character)->toBe('The Lead');
    expect($attached->pivot->order)->toEqual(0);

    // Bulk upsert() would skip Spatie's HasSlug `creating` hook and leave this blank.
    expect(CastMember::find(9000001)->slug)->not->toBeEmpty();
});

it('is idempotent when the same payload is attached twice', function () {
    $movie = Movie::firstOrFail();
    $payload = [tmdbCredit(9000003, 'Someone', 0)];

    $this->host->attach($movie, $payload);
    $countAfterFirst = $movie->cast_members()->count();

    // A retried job re-sends the same credits; the pivot's composite primary key
    // would reject a second insert of these rows.
    $this->host->attach($movie, $payload);

    expect($movie->cast_members()->count())->toBe($countAfterFirst);
});

it('collapses a person credited twice in the same payload', function () {
    $movie = Movie::firstOrFail();
    $before = $movie->cast_members()->count();

    $this->host->attach($movie, [
        tmdbCredit(9000004, 'Younger Self', 5),
        tmdbCredit(9000004, 'Older Self', 6),
    ]);

    expect($movie->cast_members()->count())->toBe($before + 1);
});

it('does nothing for an empty payload', function () {
    $movie = Movie::firstOrFail();
    $before = $movie->cast_members()->count();

    $this->host->attach($movie, []);

    expect($movie->cast_members()->count())->toBe($before);
});

it('stores an empty character when TMDB sends null or omits it, instead of failing the insert', function () {
    $movie = Movie::firstOrFail();

    $nullCharacter = tmdbCredit(9000010, 'placeholder', 0);
    $nullCharacter->character = null;

    $missingCharacter = tmdbCredit(9000011, 'placeholder', 1);
    unset($missingCharacter->character);

    $this->host->attach($movie, [$nullCharacter, $missingCharacter]);

    expect($movie->cast_members()->whereIn('cast_members.id', [9000010, 9000011])->pluck('character')->all())
        ->toBe(['', '']);
});
