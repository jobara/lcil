<?php

use App\Enums\RegimeAssessmentStatuses;
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
        '<label id="year_in_effect-label" for="year_in_effect">Year in Effect</label>',
        '<input',
        'name="year_in_effect" id="year_in_effect" type="number" min="1800" max="2030"',
        'aria-describedby="year_in_effect-hint"',
        '<p class="field__hint" id="year_in_effect-hint">',
        'YYYY format. Example: 2022.',
        '<label id="description-label" for="description">Description</label>',
        '<textarea',
        'name="description" id="description"',
        '<h2>Choose Available Law and Policy Sources</h2>',
        'Possible actions:',
        '<li>Search for sources of law and policy to add to this regime assessment.</li>',
        '<a href="'.\localized_route('lawPolicySources.create').'">Create Law and Policy Source</a> if it doesn’t already exist.',
        '<h2>Refine Selection</h2>',
        'Possible actions:',
        '<li>Refine chosen sources of law and policy by removing them from the list below.</li>',
        '<li>Add more sources of law and policy by searching above.</li>',
        '<li>Submit when done.</li>',
        '<button type="submit" form="ra-form">Submit</button>',
    ];

    $toNotSee = [
        '<input type="hidden" name="_method" value="patch">',
    ];

    $view = $this->withViewErrors([])
        ->blade('<x-forms.regime-assessment />');

    $view->assertSeeInOrder($toSee, false);

    foreach ($toNotSee as $value) {
        $view->assertDontSee($value, false);
    }
});

test('render - with existing regime assessment', function () {
    $regimeAssessment = RegimeAssessment::factory()->create([
        'jurisdiction' => 'CA-ON',
        'municipality' => 'Toronto',
        'description' => 'Test Description',
        'year_in_effect' => 2022,
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
        'name="year_in_effect"',
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

test('render - with custom id', function () {
    $toSee = [
        'id="test-ra"',
        '<button type="submit" form="test-ra">Submit</button>',
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
