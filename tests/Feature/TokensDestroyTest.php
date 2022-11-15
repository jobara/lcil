<?php

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('destroy route - required values', function () {
    $user = User::factory()->create();
    $user->createToken('test');

    $response = $this->actingAs($user)->post(route('tokens.destroy', ['id' => $user->tokens[0]->id]));

    $response->assertRedirect(localized_route('tokens.show'));
    $response->assertSessionHasNoErrors();

    $user->refresh();

    expect($user->tokens)->toHaveCount(0);
})->group('Tokens');

test('destroy route - invalid id', function () {
    $user = User::factory()->create();
    $user->createToken('test');

    $response = $this->actingAs($user)->post(route('tokens.destroy', ['id' => 20]));

    $response->assertRedirect(localized_route('tokens.show'));
    $response->assertSessionHasNoErrors();

    $user->refresh();

    expect($user->tokens)->toHaveCount(1);
    expect($user->tokens[0]->name)->toBe('test');
})->group('Tokens');

test('destroy route - unauthenticated throws AuthenticationException', function () {
    $this->withoutExceptionHandling()->post(route('tokens.destroy', ['id' => 10]));
})->throws(AuthenticationException::class)
    ->group('Tokens');

test('destroy route - unauthenticated redirected to login', function () {
    $response = $this->post(route('tokens.destroy', ['id' => 10]));
    $response->assertRedirect(localized_route('login'));
})->group('Tokens');
