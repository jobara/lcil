<?php

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('store route - required values', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('tokens.store'), ['token' => 'test']);

    $response->assertRedirect(localized_route('tokens.show').'#token-saved');
    $response->assertSessionHasNoErrors();

    $user->refresh();

    expect($user->tokens)->toHaveCount(1);
    expect($user->tokens[0]->name)->toBe('test');
})->group('Tokens');

test('store route - validation errors', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->post(route('tokens.store'), []);

    $response->assertSessionHasErrors([
        'token' => 'A Token Name (token) is required.',
    ]);
})->group('Tokens');

test('store route - unauthenticated throws AuthenticationException', function () {
    $this->withoutExceptionHandling()->get(route('tokens.store'), ['token' => 'test']);
})->throws(AuthenticationException::class)
    ->group('Tokens');

test('store route - unauthenticated redirected to login', function () {
    $response = $this->get(route('tokens.store'), ['token' => 'test']);
    $response->assertRedirect(localized_route('login'));
})->group('Tokens');
