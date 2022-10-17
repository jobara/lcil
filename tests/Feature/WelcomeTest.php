<?php

use App\Enums\RegimeAssessmentStatuses;
use App\Models\LawPolicySource;
use App\Models\Provision;
use App\Models\RegimeAssessment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use OwenIt\Auditing\Models\Audit;

uses(RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    config()->set('audit.console', true);
});

test('welcome route display', function () {
    $response = $this->get(localized_route('welcome'));

    $response->assertStatus(200);
    $response->assertViewIs('welcome');
    $response->assertViewHas('latestActivity');
    $response->assertViewHas('regimeAssessments');

    expect($response['latestActivity'])->toHaveCount(0);
    expect($response['regimeAssessments'])->toHaveCount(0);
});

test('welcome route display - authenticated users', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $responseCreateRA = $this->actingAs($user)->post(route('regimeAssessments.store'), [
        'country' => 'CA',
    ]);
    $responseCreateRA->assertSessionHasNoErrors();

    $responseCreateOtherRA = $this->actingAs($otherUser)->post(route('regimeAssessments.store'), [
        'country' => 'US',
    ]);
    $responseCreateOtherRA->assertSessionHasNoErrors();

    $response = $this->actingAs($user)->get(localized_route('welcome'));

    $response->assertStatus(200);
    $response->assertViewIs('welcome');
    $response->assertViewHas('latestActivity');
    $response->assertViewHas('regimeAssessments');

    expect($response['latestActivity'])->toHaveCount(2);
    expect($response['regimeAssessments'])->toHaveCount(1);
    expect($response['regimeAssessments']->first())->toBeInstanceOf(RegimeAssessment::class);
    expect($response['regimeAssessments']->first()->jurisdiction)->toBe('CA');
});

test('welcome route render', function () {
    $toSee = [
        '<h1 itemprop="name">Legal Capacity Inclusion Lens</h1>',
        'The Legal Capacity Inclusion Lens (LCIL) is a tool for assessing the inclusivity of legal regimes regulating legal capacity by evaluating main sources of law to established measures.',
        'The LCIL will eventually provide tools as a way for monitoring legal capacity progress across jurisdictions.',
        '<section>',
        '<h2>Search Regime Assessments</h2>',
        '<div x-data="{country: \'\'}">',
        '<form method="GET" action="'.localized_route('regimeAssessments.index'),
        '<ul role="list">',
        '<li>',
        '<label id="country-label" for="country">Country:</label>',
        '<select',
        'name="country" id="country" x-model="country"',
        '<li>',
        '<label id="subdivision-label" for="subdivision">Province / Territory:</label>',
        '<select',
        'id="subdivision"',
        'name="subdivision"',
        'x-data="{subdivision: \'\', subdivisions: {}}"',
        'x-init="',
        'subdivisions = await (async () => {await $nextTick(); return [];})();',
        'country = \'\';',
        'subdivision = \'\';',
        '$watch(\'country\', async () => {let response = country ? await axios.get(`/jurisdictions/${country}`) : {}; subdivisions = response.data ?? []; subdivision = \'\'});',
        'x-model="subdivision"',
        '<template x-if="Object.keys(subdivisions).length">',
        '<option value="">All provinces / territories</option>',
        '<template x-if="!Object.keys(subdivisions).length && country">',
        '<option value="">Not available</option>',
        '<template x-if="!Object.keys(subdivisions).length && !country">',
        '<option value="">Choose a country first</option>',
        '<template x-for="(subdivisionName, subdivisionCode) in subdivisions">',
        '<option :value="subdivisionCode" x-text="subdivisionName"></option>',
        '<li>',
        '<label id="keywords-label" for="keywords">Description contains keywords:</label>',
        '<input',
        'name="keywords" id="keywords" type="text"',
        '<li>',
        '<button type="submit">Search</button>',
    ];

    $dontSee = [
        '<h2>Your Regime Assessments</h2>',
        '<h2>Latest Activity</h2>',
    ];

    $view = $this->withViewErrors([])->view('welcome');

    $view->assertSeeInOrder($toSee, false);
    assertDontSeeAny($view, $dontSee, false);
});

test('welcome route render - authenticated', function () {
    $user = User::factory()->create();
    $toSee = [
        '<h2>Your Regime Assessments</h2>',
        '<p>You have not worked on a Regime Assessment.</p>',
        '<h2>Latest Activity</h2>',
        '<p>There is no recent activity.</p>',
    ];

    $view = $this->actingAs($user)
        ->withViewErrors([])->view('welcome', [
            'regimeAssessments' => RegimeAssessment::all(),
            'latestActivity' => Audit::all(),
        ]);

    $view->assertSeeInOrder($toSee, false);
});

test('welcome route render - authenticated with activity', function () {
    $user = User::factory()->create();
    RegimeAssessment::factory()->create();
    $toSee = [
        '<h2>Your Regime Assessments</h2>',
        '<p>You have not worked on a Regime Assessment.</p>',
        '<h2>Latest Activity</h2>',
    ];

    $view = $this->actingAs($user)
        ->withViewErrors([])->view('welcome', [
            'regimeAssessments' => [],
            'latestActivity' => Audit::all(),
        ]);

    $view->assertSeeInOrder($toSee, false);
    $view->assertDontSee('<p>There is no recent activity.</p>', false);
});

test('welcome route render - authenticated with creation activities', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $responseCreateLP = $this->actingAs($otherUser)->post(route('lawPolicySources.store'), [
        'name' => 'Test LP',
        'country' => 'CA',
    ]);
    $responseCreateLP->assertSessionHasNoErrors();
    $lpSource = LawPolicySource::first();
    $lpAudit = Audit::first();
    $lpAudit->created_at = Carbon::now()->addDays(-2);
    $lpAudit->save();

    $responseCreateProv = $this->actingAs($user)->post(route('provisions.store', ['lawPolicySource' => $lpSource]), [
        'section' => '2',
        'body' => 'test provision',
    ]);
    $responseCreateProv->assertSessionHasNoErrors();
    $provision = Provision::first();
    $provAudit = Audit::skip(1)->first();
    $provAudit->created_at = Carbon::now()->addDays(-1);
    $provAudit->save();

    $responseCreateRA = $this->actingAs($user)->post(route('regimeAssessments.store'), [
        'country' => 'CA',
    ]);
    $responseCreateRA->assertSessionHasNoErrors();
    $regimeAssessment = RegimeAssessment::first();
    $regimeAssessment->lawPolicySources()->attach($lpSource);
    $raAudit = Audit::skip(2)->first();
    $raAudit->created_at = Carbon::now()->addHours(-10);
    $raAudit->save();

    $responseCreateOtherRA = $this->actingAs($otherUser)->post(route('regimeAssessments.store'), [
        'country' => 'CA',
    ]);
    $responseCreateOtherRA->assertSessionHasNoErrors();
    $regimeAssessmentOther = RegimeAssessment::latest()->first();
    $raOtherAudit = Audit::skip(3)->first();
    $raOtherAudit->created_at = Carbon::now()->addMinutes(-10);
    $raOtherAudit->save();

    $toSee = [
        '<h2>Your Regime Assessments</h2>',
        '<p>You have not worked on a Regime Assessment.</p>',
        '<h2>Latest Activity</h2>',

        // Regime Assessment Other
        '10 minutes ago -',
        '<a href="'.localized_route('regimeAssessments.show', $regimeAssessmentOther),
        'Canada',
        "regime assessment created by {$otherUser->name}",

        // Regime Assessment
        '10 hours ago -',
        '<a href="'.localized_route('regimeAssessments.show', $regimeAssessment),
        'Canada',
        "regime assessment created by {$user->name}",

        // Provision
        "{$provAudit->created_at->format('Y-m-d')} -",
        "Provision {$provision->section} added to",
        '<a href="'.localized_route('lawPolicySources.show', $provision->lawPolicySource),
        $lpSource->name,
        "by {$user->name}",

        // Law Policy Source
        "{$lpAudit->created_at->format('Y-m-d')} -",
        '<a href="'.localized_route('lawPolicySources.show', $lpSource),
        $lpSource->name,
        "law or policy source created by {$otherUser->name}",
    ];

    $dontSee = [
        'regime assessment modified by',
        "Provision {$provision->section} of",
        'law or policy source modified by',
        '<p>There is no recent activity.</p>',
    ];

    $view = $this->actingAs($user)
        ->withViewErrors([])->view('welcome', [
            'regimeAssessments' => [],
            'latestActivity' => Audit::latest()->get(),
        ]);

    $view->assertSeeInOrder($toSee, false);
    assertDontSeeAny($view, $dontSee, false);
});

test('welcome route render - authenticated with updated activities', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $lpSource = LawPolicySource::factory()->create([
        'name' => 'Test LP',
        'jurisdiction' => 'CA',
    ]);

    $responseUpdateLP = $this->actingAs($otherUser)->patch(route('lawPolicySources.update', ['lawPolicySource' => $lpSource]), [
        'name' => 'Test LP',
        'country' => 'CA',
        'is_core' => true,
    ]);
    $responseUpdateLP->assertSessionHasNoErrors();

    $lpAuditCreate = Audit::first();
    $lpAuditCreate->created_at = Carbon::now()->addDays(-4);
    $lpAuditCreate->save();
    $lpAuditUpdate = Audit::skip(1)->first();
    $lpAuditUpdate->created_at = Carbon::now()->addDays(-2);
    $lpAuditUpdate->save();

    $provision = Provision::factory()
        ->for($lpSource)
        ->create([
            'section' => '2b',
            'body' => 'test provision',
        ]);

    $responseUpdateProv = $this->actingAs($user)->patch(route('provisions.update', ['lawPolicySource' => $lpSource, 'slug' => $provision->slug]), [
        'section' => '2b',
        'body' => 'test provision update',
    ]);
    $responseUpdateProv->assertSessionHasNoErrors();

    $provAuditCreate = Audit::skip(2)->first();
    $provAuditCreate->created_at = Carbon::now()->addDays(-4);
    $provAuditCreate->save();
    $provAuditUpdate = Audit::skip(3)->first();
    $provAuditUpdate->created_at = Carbon::now()->addHours(-1);
    $provAuditUpdate->save();

    $regimeAssessment = RegimeAssessment::factory()
        ->create([
            'jurisdiction' => 'CA',
        ]);
    $regimeAssessment->lawPolicySources()->attach($lpSource);

    $responseUpdateRA = $this->actingAs($user)->patch(route('regimeAssessments.update', ['regimeAssessment' => $regimeAssessment]), [
        'country' => 'CA',
        'subdivision' => 'ON',
    ]);
    $responseUpdateRA->assertSessionHasNoErrors();
    $raAuditCreate = Audit::skip(4)->first();
    $raAuditCreate->created_at = Carbon::now()->addDays(-2);
    $raAuditCreate->save();
    $raAuditUpdate = Audit::skip(5)->first();
    $raAuditUpdate->created_at = Carbon::now()->addMinutes(-5);
    $raAuditUpdate->save();

    $toSee = [
        '<h2>Your Regime Assessments</h2>',
        '<p>You have not worked on a Regime Assessment.</p>',
        '<h2>Latest Activity</h2>',

        // Regime Assessment Update
        '5 minutes ago -',
        '<a href="'.localized_route('regimeAssessments.show', $regimeAssessment),
        'Ontario, Canada',
        "regime assessment modified by {$user->name}",

        // Provision
        '1 hours ago -',
        "Provision {$provision->section} of",
        '<a href="'.localized_route('lawPolicySources.show', $provision->lawPolicySource),
        $lpSource->name,
        "by {$user->name}",

        // Law Policy Source
        "{$lpAuditUpdate->created_at->format('Y-m-d')} -",
        '<a href="'.localized_route('lawPolicySources.show', $lpSource),
        $lpSource->name,
        "law or policy source modified by {$otherUser->name}",

        // Regime Assessment
        "{$raAuditCreate->created_at->format('Y-m-d')} -",
        '<a href="'.localized_route('regimeAssessments.show', $regimeAssessment),
        'Canada',
        "regime assessment created by {$user->name}",
    ];

    $dontSee = [
        'law or policy source created by',
        "Provision {$provision->section} added to",
        '<p>There is no recent activity.</p>',
    ];

    $view = $this->actingAs($user)
        ->withViewErrors([])->view('welcome', [
            'regimeAssessments' => [],
            'latestActivity' => Audit::latest()->take(4)->get(),
        ]);

    $view->assertSeeInOrder($toSee, false);
    assertDontSeeAny($view, $dontSee, false);
});

test('welcome route render - authenticated with Regime Assessment', function () {
    $user = User::factory()->create();

    $responseCreateRA = $this->actingAs($user)->post(route('regimeAssessments.store'), [
        'country' => 'CA',
        'status' => RegimeAssessmentStatuses::Draft->value,
    ]);
    $responseCreateRA->assertSessionHasNoErrors();

    $toSee = [
        '<h2>Your Regime Assessments</h2>',
        '<h3>',
        'Canada',
        '(Draft)',
        '<h2>Latest Activity</h2>',
    ];

    $dontSee = [
        '<p>You have not worked on a Regime Assessment.</p>',
        '<p>There is no recent activity.</p>',
    ];

    $view = $this->actingAs($user)
        ->withViewErrors([])->view('welcome', [
            'regimeAssessments' => RegimeAssessment::where('jurisdiction', 'CA')->get(),
            'latestActivity' => Audit::latest()->get(),
        ]);

    $view->assertSeeInOrder($toSee, false);
    assertDontSeeAny($view, $dontSee, false);
});
