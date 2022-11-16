<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('CreateNewUser', function ($config, $expected) {
    config($config);
    $response = $this->post(localized_route('register.store'), [
        'name' => 'tester',
        'email' => 'tester@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'locale' => 'en',
    ]);

    if ($expected) {
        $response->assertSessionHasNoErrors();
    } else {
        $response->assertSessionHasErrors(['email' => __('At the moment registration for :app is restricted.', ['app' => config('app.name')])]);
    }
})->with('emailRestrictions');

test('UpdateUserProfileInformation', function ($config, $expected) {
    $user = User::factory()->create();
    config($config);
    $response = $this->actingAs($user)->put(localized_route('user-profile-information.update'), [
        'name' => $user->name,
        'email' => 'tester@example.com',
        'locale' => $user->locale,
    ]);

    if ($expected) {
        $response->assertSessionHasNoErrors();
    } else {
        $response->assertSessionHasErrorsIn('updateProfileInformation', ['email' => __('At the moment registration for :app is restricted.', ['app' => config('app.name')])]);
    }
})->with('emailRestrictions');
