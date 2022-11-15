<?php

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('api route', function () {
    $user = User::factory()->create();
    $user->createToken('test');

    $response = $this->actingAs($user)->get(localized_route('api.show'));

    $response->assertStatus(200);
    $response->assertViewIs('api-docs');
    $response->assertViewHas('endPoints');
})->group('Tokens');

test('api route - unauthenticated throws AuthenticationException', function () {
    $this->withoutExceptionHandling()->get(localized_route('api.show'));
})->throws(AuthenticationException::class)
    ->group('RegimeAssessments');

test('api route - unauthenticated redirected to login', function () {
    $response = $this->get(localized_route('api.show'));
    $response->assertRedirect(localized_route('login'));
})->group('RegimeAssessments');
