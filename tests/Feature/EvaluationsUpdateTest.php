<?php

use App\Enums\EvaluationAssessments;
use App\Models\Evaluation;
use App\Models\LawPolicySource;
use App\Models\Measure;
use App\Models\Provision;
use App\Models\RegimeAssessment;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('update route - insert', function () {
    $user = User::factory()->create();
    $measure = Measure::factory()->create();
    $regimeAssessment = RegimeAssessment::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create();
    $provision = Provision::factory()
        ->for($lawPolicySource)
        ->create([
            'section' => '12',
        ]);
    $regimeAssessment->lawPolicySources()->attach($lawPolicySource);

    $data = [
        'evaluations' => [
            $provision->id => [
                'assessment' => EvaluationAssessments::Fully->value,
                'comment' => 'Test comment',
                'provision_id' => "{$provision->id}",
            ],
        ],
    ];

    $response = $this->actingAs($user)->post(route('evaluations.update', [
        'regimeAssessment' => $regimeAssessment,
        'measure' => $measure,
    ]), $data);

    $evaluations = Evaluation::all();
    $evaluation = $evaluations->first();

    $response->assertRedirect(\localized_route('evaluations.show', [
        'regimeAssessment' => $regimeAssessment,
        'measure' => $measure,
    ]).'#save__message');

    $response->assertSessionHasNoErrors();
    $response->assertSessionHas('status', 'saved');

    expect($evaluations)->toHaveCount(1);
    expect($evaluation->assessment->value)->toBe($data['evaluations'][$provision->id]['assessment']);
    expect($evaluation->comment)->toBe($data['evaluations'][$provision->id]['comment']);
    expect($evaluation->regimeAssessment)->is($regimeAssessment)->toBeTrue();
    expect($evaluation->measure)->is($measure)->toBeTrue();
    expect($evaluation->provision)->is($provision)->toBeTrue();
})->group('evaluations');

test('update route - update', function () {
    $user = User::factory()->create();
    $measure = Measure::factory()->create();
    $regimeAssessment = RegimeAssessment::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create();
    $provision = Provision::factory()
        ->for($lawPolicySource)
        ->create([
            'section' => '12',
        ]);
    $regimeAssessment->lawPolicySources()->attach($lawPolicySource);
    $original = Evaluation::factory()
        ->for($regimeAssessment)
        ->for($measure)
        ->for($provision)
        ->create([
            'assessment' => EvaluationAssessments::Partially->value,
            'comment' => 'Initial comment',
        ]);

    $data = [
        'evaluations' => [
            $provision->id => [
                'assessment' => EvaluationAssessments::Fully->value,
                'comment' => 'New comment',
                'provision_id' => "{$provision->id}",
            ],
        ],
    ];

    $response = $this->actingAs($user)->post(route('evaluations.update', [
        'regimeAssessment' => $regimeAssessment,
        'measure' => $measure,
    ]), $data);

    $evaluations = Evaluation::all();
    $evaluation = $evaluations->first();

    $response->assertRedirect(\localized_route('evaluations.show', [
        'regimeAssessment' => $regimeAssessment,
        'measure' => $measure,
    ]).'#save__message');

    $response->assertSessionHasNoErrors();
    $response->assertSessionHas('status', 'saved');

    expect($evaluations)->toHaveCount(1);
    expect($evaluation->assessment->value)->toBe($data['evaluations'][$provision->id]['assessment']);
    expect($evaluation->comment)->toBe($data['evaluations'][$provision->id]['comment']);
    expect($evaluation->regimeAssessment)->is($regimeAssessment)->toBeTrue();
    expect($evaluation->measure)->is($measure)->toBeTrue();
    expect($evaluation->provision)->is($provision)->toBeTrue();
    expect($evaluation)->is($original)->toBeTrue();
})->group('evaluations');

test('update route - delete', function () {
    $user = User::factory()->create();
    $measure = Measure::factory()->create();
    $regimeAssessment = RegimeAssessment::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create();
    $provision = Provision::factory()
        ->for($lawPolicySource)
        ->create([
            'section' => '12',
        ]);
    $regimeAssessment->lawPolicySources()->attach($lawPolicySource);
    Evaluation::factory()
        ->for($regimeAssessment)
        ->for($measure)
        ->for($provision)
        ->create([
            'assessment' => EvaluationAssessments::Partially->value,
            'comment' => 'Initial comment',
        ]);

    $data = [
        'evaluations' => [
            $provision->id => [
                'provision_id' => "{$provision->id}",
            ],
        ],
    ];

    $response = $this->actingAs($user)->post(route('evaluations.update', [
        'regimeAssessment' => $regimeAssessment,
        'measure' => $measure,
    ]), $data);

    $evaluations = Evaluation::all();

    $response->assertRedirect(\localized_route('evaluations.show', [
        'regimeAssessment' => $regimeAssessment,
        'measure' => $measure,
    ]).'#save__message');

    $response->assertSessionHasNoErrors();
    $response->assertSessionHas('status', 'saved');

    expect($evaluations)->toHaveCount(0);
})->group('evaluations');

test('update route - missing provision', function () {
    $user = User::factory()->create();
    $measure = Measure::factory()->create();
    $regimeAssessment = RegimeAssessment::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create();
    $regimeAssessment->lawPolicySources()->attach($lawPolicySource);

    $data = [
        'evaluations' => [
            10 => [
                'provision_id' => '10',
            ],
        ],
    ];

    $response = $this->actingAs($user)->post(route('evaluations.update', [
        'regimeAssessment' => $regimeAssessment,
        'measure' => $measure,
    ]), $data);

    $response->assertNotFound();

    $evaluations = Evaluation::all();
    expect($evaluations)->toHaveCount(0);
})->group('evaluations');

test('update route - no evaluation sent', function () {
    $user = User::factory()->create();
    $measure = Measure::factory()->create();
    $regimeAssessment = RegimeAssessment::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create();
    Provision::factory()
        ->for($lawPolicySource)
        ->create([
            'section' => '12',
        ]);
    $regimeAssessment->lawPolicySources()->attach($lawPolicySource);

    $data = [];

    $response = $this->actingAs($user)->post(route('evaluations.update', [
        'regimeAssessment' => $regimeAssessment,
        'measure' => $measure,
    ]), $data);

    $evaluations = Evaluation::all();

    $response->assertRedirect(\localized_route('evaluations.show', [
        'regimeAssessment' => $regimeAssessment,
        'measure' => $measure,
    ]).'#save__message');

    $response->assertSessionHasNoErrors();
    $response->assertSessionHas('status', 'saved');

    expect($evaluations)->toHaveCount(0);
})->group('evaluations');

test('update route - validation errors', function ($data, $errors) {
    $user = User::factory()->create();
    $measure = Measure::factory()->create();
    $regimeAssessment = RegimeAssessment::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create();
    $provision = Provision::factory()
        ->for($lawPolicySource)
        ->create([
            'section' => '12',
        ]);
    $regimeAssessment->lawPolicySources()->attach($lawPolicySource);

    $response = $this->actingAs($user)->post(route('evaluations.update', [
        'regimeAssessment' => $regimeAssessment,
        'measure' => $measure,
    ]), $data($provision->id));

    $expandedErrors = [];

    foreach ($errors as $name => $message) {
        $expandedErrors[sprintf($name, $provision->id)] = $message;
    }

    $response->assertSessionHasErrors($expandedErrors);
})->with('evaluationValidationErrors')
    ->group('evaluations');

test('update route - unauthenticated throws AuthenticationException', function () {
    $measure = Measure::factory()->create();
    $regimeAssessment = RegimeAssessment::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create();
    $provision = Provision::factory()
        ->for($lawPolicySource)
        ->create([
            'section' => '12',
        ]);
    $regimeAssessment->lawPolicySources()->attach($lawPolicySource);

    $data = [
        'evaluations' => [
            $provision->id => [
                'assessment' => EvaluationAssessments::Fully->value,
                'comment' => 'Test comment',
                'provision_id' => "{$provision->id}",
            ],
        ],
    ];

    $this->withoutExceptionHandling()->post(route('evaluations.update', [
        'regimeAssessment' => $regimeAssessment,
        'measure' => $measure,
    ]), $data);
})->throws(AuthenticationException::class)
    ->group('evaluations');

test('update route - unauthenticated redirected to login', function () {
    $measure = Measure::factory()->create();
    $regimeAssessment = RegimeAssessment::factory()->create();
    $lawPolicySource = LawPolicySource::factory()->create();
    $provision = Provision::factory()
        ->for($lawPolicySource)
        ->create([
            'section' => '12',
        ]);
    $regimeAssessment->lawPolicySources()->attach($lawPolicySource);

    $data = [
        'evaluations' => [
            $provision->id => [
                'assessment' => EvaluationAssessments::Fully->value,
                'comment' => 'Test comment',
                'provision_id' => "{$provision->id}",
            ],
        ],
    ];

    $response = $this->post(route('evaluations.update', [
        'regimeAssessment' => $regimeAssessment,
        'measure' => $measure,
    ]), $data);

    $response->assertRedirect(\localized_route('login'));

    $evaluations = Evaluation::all();
    expect($evaluations)->toHaveCount(0);
})->group('evaluations');
