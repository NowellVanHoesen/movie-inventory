<?php

use App\Models\User;
use Tests\Browser;

// Note: a full "type valid credentials and log in successfully" scenario isn't
// covered here. The Dusk browser hits the site's live server process, which
// reads a different database connection than the one this test process writes
// factory users into, so a freshly created user isn't visible to the server
// for a real credentialed login. The success path is covered server-side by
// tests/Feature/auth/AuthenticationTest.php; the modal-close/stay-on-page
// behavior should also be checked by hand per the feature's verification steps.

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
