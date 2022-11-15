<?php

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('show route', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(localized_route('tokens.show'));

    $response->assertStatus(200);
    $response->assertViewIs('tokens');
    $response->assertViewHas('tokens');
    $response->assertSee('No tokens available.', false);
    $response->assertDontSee('<table>', false);

    expect($response['tokens'])->toHaveCount(0);
})->group('Tokens');

test('show route with tokens', function () {
    $user = User::factory()->create();
    $user->createToken('test');

    $toSee = [
        '<form',
        'action',
        route('tokens.destroy', $user->tokens[0]->id),
        '<button',
        'Revoke test token',
    ];

    $response = $this->actingAs($user)->get(localized_route('tokens.show'));

    $response->assertStatus(200);
    $response->assertViewIs('tokens');
    $response->assertViewHas('tokens');
    $response->assertSeeInOrder($toSee, false);
    $response->assertDontSee('No tokens available.', false);

    expect($response['tokens'])->toHaveCount(1);
    expect($response['tokens'][0]->name)->toBe($user->tokens[0]->name);
})->group('Tokens');

test('show route - unauthenticated throws AuthenticationException', function () {
    $this->withoutExceptionHandling()->get(localized_route('tokens.show'));
})->throws(AuthenticationException::class)
    ->group('Tokens');

test('show route - unauthenticated redirected to login', function () {
    $response = $this->get(localized_route('tokens.show'));
    $response->assertRedirect(localized_route('login'));
})->group('Tokens');
