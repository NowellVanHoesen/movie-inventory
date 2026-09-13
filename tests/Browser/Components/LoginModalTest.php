<?php

use App\Models\User;
use Tests\Browser;

// Note: a full "type valid credentials and log in successfully" scenario isn't
// covered here — not currently attempted, though tests/DuskTestCase.php now
// points this process's queries at the same movie_inventory_dusk database the
// live Herd site reads during `php artisan dusk` (see its $exceptTables /
// refreshApplication comments), so a user created here is visible to it. The
// success path is covered server-side by tests/Feature/auth/AuthenticationTest.php;
// the modal-close/stay-on-page behavior should also be checked by hand per the
// feature's verification steps.

it('shows a validation error and keeps the modal open over the original page on bad credentials', function () {
    $user = User::factory()->create();

    $this->browse(function (Browser $browser) use ($user) {
        $browser
            ->visit(route('movies.index'))
            ->waitFor('@login-nav-btn')
            ->press('@login-nav-btn')
            ->waitFor('@login-submit-btn')
            ->type('@login-email-input', $user->email)
            ->type('@login-password-input', 'wrong-password')
            ->press('@login-submit-btn')
            ->waitForText('These credentials do not match our records.')
            ->assertUrlIs(route('login'))
            ->press('@login-cancel-btn')
            ->waitUntilMissingModal()
            ->pause(200)
            ->assertUrlIs(route('movies.index'));
    });
});
