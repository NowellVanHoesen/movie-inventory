<?php

use App\Models\User;

use function Pest\Laravel\get;
use function Pest\Laravel\post;

it('renders the login modal over the resolved base page', function () {
    get(route('login'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('_modal.component', 'Auth/Login')
        );
});

it('authenticates users via the login screen', function () {
    $user = User::factory()->create();

    $response = post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('home'));
});

it('does not authenticate with an invalid password', function () {
    $user = User::factory()->create();

    $response = post(route('login'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
    $response->assertRedirect(route('login'));
});

it('logs out and stays on the page the user was on', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->withHeader('referer', route('movies.index'))
        ->post(route('logout'));

    $this->assertGuest();
    $response->assertRedirect(route('movies.index'));
});

it('logs out to the home page when there is nowhere to go back to', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $this->assertGuest();
    $response->assertRedirect('/');
});
