<?php

use App\Enums\RegimeAssessmentStatuses;
use App\Models\LawPolicySource;
use App\Models\RegimeAssessment;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('render - without existing regime assessment', function () {
    $toSee = [
        '<form',
        'id="ra-form" method="POST" action="'.route('regimeAssessments.store'),
        '<input',
        'name="status" id="status" type="hidden" value="draft"',
        '<label id="country-label" for="country">Country (required)</label>',
        '<select',
        'name="country"',
        'id="country"',
        '<option value="" selected></option>',
        '<label id="subdivision-label" for="subdivision">Province / Territory</label>',
        '<select',
        'id="subdivision"',
        'name="subdivision"',
        '<label id="municipality-label" for="municipality">Municipality</label>',
        '<input',
        'name="municipality" id="municipality" type="text"',
        'aria-describedby="municipality-hint"',
        '<p class="field__hint" id="municipality-hint">',
        'Requires a Province / Territory to be selected',
        '<label id="year_of_assessment-label" for="year_of_assessment">Year of Assessment</label>',
        '<input',
        'name="year_of_assessment" id="year_of_assessment" type="number" min="1800" max="2030"',
        'aria-describedby="year_of_assessment-hint"',
        '<p class="field__hint" id="year_of_assessment-hint">',
        'YYYY format. Example: 2022.',
        '<label id="description-label" for="description">Description</label>',
        '<textarea',
        'name="description" id="description"',
        '<h2 id="choose-law-policy-source">Choose Available Law and Policy Sources</h2>',
        'Possible actions:',
        '<li>Select sources of law and policy to add to this regime assessment.</li>',
        '<a href="'.\localized_route('lawPolicySources.create').'">Create Law and Policy Source</a> if it doesn’t already exist.',
        '<a href="'.\localized_route('lawPolicySources.create').'">Create Law and Policy Source</a>',
        '<button type="submit">Submit</button>',
    ];

    $view = $this->withViewErrors([])
        ->blade('<x-forms.regime-assessment />');

    $view->assertSeeInOrder($toSee, false);
    $view->assertDontSee('<input type="hidden" name="_method" value="patch">', false);
});

test('render - without existing regime assessment - has law policy sources', function () {
    LawPolicySource::factory()->create();

    $lawPolicySources = LawPolicySource::all()->sortBy([
        ['jurisdiction', 'asc'],
        ['municipality', 'asc'],
        ['name', 'asc'],
    ])->all();

    $toSee = [
        '<form',
        'id="ra-form" method="POST" action="'.route('regimeAssessments.store'),
        '<input',
        'name="status" id="status" type="hidden" value="draft"',
        '<label id="country-label" for="country">Country (required)</label>',
        '<select',
        'name="country"',
        'id="country"',
        '<option value="" selected></option>',
        '<label id="subdivision-label" for="subdivision">Province / Territory</label>',
        '<select',
        'id="subdivision"',
        'name="subdivision"',
        '<label id="municipality-label" for="municipality">Municipality</label>',
        '<input',
        'name="municipality" id="municipality" type="text"',
        'aria-describedby="municipality-hint"',
        '<p class="field__hint" id="municipality-hint">',
        'Requires a Province / Territory to be selected',
        '<label id="year_of_assessment-label" for="year_of_assessment">Year of Assessment</label>',
        '<input',
        'name="year_of_assessment" id="year_of_assessment" type="number" min="1800" max="2030"',
        'aria-describedby="year_of_assessment-hint"',
        '<p class="field__hint" id="year_of_assessment-hint">',
        'YYYY format. Example: 2022.',
        '<label id="description-label" for="description">Description</label>',
        '<textarea',
        'name="description" id="description"',
        '<h2 id="choose-law-policy-source">Choose Available Law and Policy Sources</h2>',
        'Possible actions:',
        '<li>Select sources of law and policy to add to this regime assessment.</li>',
        '<a href="'.\localized_route('lawPolicySources.create').'">Create Law and Policy Source</a> if it doesn’t already exist.',
        '<div role="group" aria-labelledby="choose-law-policy-source">',
        '<button type="submit">Submit</button>',
    ];

    $dontSee = [
        '<input type="hidden" name="_method" value="patch">',
        'checked',
    ];

    $view = $this->withViewErrors([])
        ->blade('<x-forms.regime-assessment :lawPolicySources="$lawPolicySources" />',
            [
                'lawPolicySources' => $lawPolicySources,
            ]
        );

    $view->assertSeeInOrder($toSee, false);

    assertDontSeeAny($view, $dontSee, false);
});

test('render - with existing regime assessment', function () {
    $regimeAssessment = RegimeAssessment::factory()->create([
        'jurisdiction' => 'CA-ON',
        'municipality' => 'Toronto',
        'description' => 'Test Description',
        'year_of_assessment' => 2022,
        'status' => RegimeAssessmentStatuses::Published->value,
    ]);
    $toSee = [
        '<form',
        'method="POST" action="'.route('regimeAssessments.update', $regimeAssessment),
        '<input type="hidden" name="_method" value="patch">',
        '<input',
        'name="status" id="status" type="hidden" value="'.$regimeAssessment->status->value.'"',
        'name="country"',
        '<option value="'.parse_country_code($regimeAssessment->jurisdiction).'" selected',
        'name="subdivision"',
        'country = \''.parse_country_code($regimeAssessment->jurisdiction).'\';',
        'subdivision = \''.parse_subdivision_code($regimeAssessment->jurisdiction).'\';',
        'name="municipality"',
        $regimeAssessment->municipality,
        'name="year_of_assessment"',
        'name="description" id="description"',
        $regimeAssessment->description.'</textarea>',
    ];

    $view = $this->withViewErrors([])
        ->blade(
            '<x-forms.regime-assessment :regimeAssessment="$regimeAssessment" />',
            ['regimeAssessment' => $regimeAssessment]
        );

    $view->assertSeeInOrder($toSee, false);
});

test('render - with existing regime assessment - has law policy sources', function () {
    $lpSource = LawPolicySource::factory()->create();

    $lawPolicySources = LawPolicySource::all()->sortBy([
        ['jurisdiction', 'asc'],
        ['municipality', 'asc'],
        ['name', 'asc'],
    ])->all();

    $regimeAssessment = RegimeAssessment::factory()->create([
        'jurisdiction' => 'CA-ON',
        'municipality' => 'Toronto',
        'description' => 'Test Description',
        'year_of_assessment' => 2022,
        'status' => RegimeAssessmentStatuses::Published->value,
    ]);

    $regimeAssessment->lawPolicySources()->attach($lpSource);

    $toSee = [
        '<form',
        'method="POST" action="'.route('regimeAssessments.update', $regimeAssessment),
        '<input type="hidden" name="_method" value="patch">',
        '<input',
        'name="status" id="status" type="hidden" value="'.$regimeAssessment->status->value.'"',
        'name="country"',
        '<option value="'.parse_country_code($regimeAssessment->jurisdiction).'" selected',
        'name="subdivision"',
        'country = \''.parse_country_code($regimeAssessment->jurisdiction).'\';',
        'subdivision = \''.parse_subdivision_code($regimeAssessment->jurisdiction).'\';',
        'name="municipality"',
        $regimeAssessment->municipality,
        'name="year_of_assessment"',
        'name="description" id="description"',
        $regimeAssessment->description.'</textarea>',
        '<div role="group" aria-labelledby="choose-law-policy-source">',
        '<input type="checkbox"',
        'name="lawPolicySources['.$lpSource->id.']" id="lawPolicySources['.$lpSource->id.']"',
        'value="1"',
        'checked',
    ];

    $view = $this->withViewErrors([])
        ->blade(
            '<x-forms.regime-assessment :regimeAssessment="$regimeAssessment" :lawPolicySources="$lawPolicySources" />',
            [
                'regimeAssessment' => $regimeAssessment,
                'lawPolicySources' => $lawPolicySources,
            ]
        );

    $view->assertSeeInOrder($toSee, false);
});

test('render - with custom id', function () {
    $toSee = [
        'id="test-ra"',
    ];

    $view = $this->withViewErrors([])
        ->blade('<x-forms.regime-assessment id="test-ra" />');

    $view->assertSeeInOrder($toSee, false);
});

test('render - errors', function ($data, $errors) {
    foreach ($errors as $key => $message) {
        $toSee[] = "id=\"{$key}";

        // status input is hidden so does not have an error message
        if ($key !== 'status') {
            $toSee[] = 'aria-describedby';
            $toSee[] = "{$key}-error";
            $toSee[] = 'aria-invalid="true"';
            $toSee[] = "<p class=\"field__error\" id=\"{$key}-error\">";
            $toSee[] = $message;
        }
    }

    $view = $this->withViewErrors($errors)
        ->blade('<x-forms.regime-assessment />');

    $view->assertSeeInOrder($toSee, false);
})->with('regimeAssessmentValidationErrors');
