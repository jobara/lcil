<?php

use App\Models\LawPolicySource;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('render - without existing law policy source', function () {
    $toSee = [
        '<form',
        'method="POST" action="' . route('lawPolicySources.store'),
        '<label id="name-label" for="name">Law or Policy Name (required)</label>',
        '<input',
        'name="name" id="name" type="text"',
        'required',
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
        '<label id="reference-label" for="reference">Reference / Link</label>',
        '<input',
        'name="reference" id="reference" type="url"',
        'aria-describedby="reference-hint"',
        '<p class="field__hint" id="reference-hint">',
        'Web link or URL to source. Example: https://www.example.com/',
        '<label id="type-label" for="type">Type</label>',
        '<select',
        'name="type" id="type"',
        '<option value="" selected></option>',
        '<option value="statute" >Statute</option>',
        '<option value="policy" >Policy</option>',
        '<option value="constitutional" >Constitutional</option>',
        '<option value="case_law" >Case Law</option>',
        '<option value="regulation" >Regulation</option>',
        '<option value="quasi-constitutional" >Quasi-Constitutional</option>',
        '<legend id="is_core-label">Effect on Legal Capacity</legend>',
        '<input  type="radio" name="is_core" id="is_core-1" value="1"',
        '<label for="is_core-1">',
        'Core - directly affects legal capacity',
        '<input  type="radio" name="is_core" id="is_core-0" value="0"',
        '<label for="is_core-0">',
        'Supplemental - indirectly affects legal capacity',
        '<a href="' . \localized_route('lawPolicySources.index') . '">Cancel</a>',
        '<button type="submit">Submit</button>',
    ];

    $toNotSee = [
        '<input type="hidden" name="_method" value="patch">',
    ];

    $view = $this->withViewErrors([])
                 ->blade('<x-forms.law-policy-source />');

    $view->assertSeeInOrder($toSee, false);

    foreach ($toNotSee as $value) {
        $view->assertDontSee($value, false);
    }
});

test('render - with existing law policy source', function () {
    $lawPolicySource = LawPolicySource::factory()->create([
        'name' => 'test',
        'type' => 'statute',
        // once Hearth has been updated to v3+ we should be able to use `true` instead of 1.
        // see Hearth PR# 144: https://github.com/fluid-project/hearth/pull/144
        'is_core' => 1,
        'reference' => 'http://example.com',
        'jurisdiction' => 'CA-ON',
        'municipality' => 'Toronto',
        'year_in_effect' => 2022,
    ]);
    $toSee = [
        '<form',
        'method="POST" action="' . route('lawPolicySources.update', $lawPolicySource),
        '<input type="hidden" name="_method" value="patch">',
        'name="name"',
        $lawPolicySource->name,
        'name="country"',
        '<option value="' . parse_country_code($lawPolicySource->jurisdiction) . '" selected',
        'name="subdivision"',
        'country = \'' . parse_country_code($lawPolicySource->jurisdiction) . '\';',
        'subdivision = \'' . parse_subdivision_code($lawPolicySource->jurisdiction) . '\';',
        'name="municipality"',
        $lawPolicySource->municipality,
        'name="year_in_effect"',
        "value=\"{$lawPolicySource->year_in_effect}\"",
        'name="reference"',
        "value=\"{$lawPolicySource->reference}\"",
        'name="type"',
        "<option value=\"{$lawPolicySource->type->value}\" selected",
        'name="is_core"',
        'value="' . $lawPolicySource->is_core . '"  checked',
        '<a href="' . \localized_route('lawPolicySources.show', $lawPolicySource) . '">Cancel</a>',
    ];

    $view = $this->withViewErrors([])
                 ->blade(
                    '<x-forms.law-policy-source :lawPolicySource="$lawPolicySource" />',
                    ['lawPolicySource' => $lawPolicySource]
                );

    $view->assertSeeInOrder($toSee, false);
});

test('render - errors', function ($data, $errors, $anchors = []) {
    foreach ($errors as $key => $message) {
        $id = $anchors[$key] ?? $key;
        $toSee[] = "id=\"{$id}";
        $toSee[] = 'aria-describedby';
        $toSee[] = "{$key}-error";
        $toSee[] = 'aria-invalid="true"';
        $toSee[] = "<p class=\"field__error\" id=\"{$key}-error\">";
        $toSee[] = $message;
    }

    $view = $this->withViewErrors($errors)
                 ->blade('<x-forms.law-policy-source />');

    $view->assertSeeInOrder($toSee, false);
})->with('lawPolicySourceValidationErrors')
  ->group('LawPolicySources');
