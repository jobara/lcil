<?php

namespace Tests\Feature;

use App\Enums\ApproachToLegalCapacityEnum;
use App\Enums\DecisionMakingCapabilityEnum;
use App\Enums\LawPolicyTypeEnum;
use App\Enums\ProvisionDecisionTypeEnum;
use App\Models\LawPolicySource;
use App\Models\Provision;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LawPolicySourcesViewTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     *
     * @return void
     */
    public function test_show_route()
    {
        // create a Law and Policy Source to use for the test
        $lawPolicySource = LawPolicySource::factory()
            ->has(Provision::factory()->count(3))
            ->create();

        $response = $this->get(localized_route('law-policy-sources.show', $lawPolicySource));

        $response->assertStatus(200);
        $response->assertViewIs('law-policy-sources.show');
        $response->assertViewHas('lawPolicySource');

        $this->assertInstanceOf(LawPolicySource::class, $response['lawPolicySource']);
    }

    /**
     *
     * @return void
     */
    public function test_a_view_with_all_fields_render()
    {
        // create a Law and Policy Source to use for the test
        $lawPolicySource = LawPolicySource::factory()
            ->create([
                'type' => $this->faker->randomElement(LawPolicyTypeEnum::values()),
                'is_core' => $this->faker->boolean(),
                'reference' => $this->faker->unique()->url(),
                'jurisdiction' => "CA-ON",
                'municipality' => ucfirst($this->faker->word()),
            ]);

        $provisionDecisionTypes = ProvisionDecisionTypeEnum::values();
        $provisionConfig = [
            'decision_type' => $this->faker->randomElements($provisionDecisionTypes, $this->faker->numberBetween(1, count($provisionDecisionTypes))),
            'legal_capacity_approach' => $this->faker->randomElement(ApproachToLegalCapacityEnum::values()),
            'decision_making_capability' => $this->faker->randomElement(DecisionMakingCapabilityEnum::values()),
            'reference' => $this->faker->unique()->url(),
            'is_subject_to_challenge' => true,
            'is_result_of_challenge' => false,
            'decision_citation' => $this->faker->paragraph(),
        ];

        $provisionConfigAlternate = array_merge($provisionConfig, [
            'is_subject_to_challenge' => false,
            'is_result_of_challenge' => true,
        ]);

        Provision::factory()
            ->for($lawPolicySource)
            ->create($provisionConfig);

        Provision::factory()
            ->for($lawPolicySource)
            ->create($provisionConfigAlternate);

        $strings = [
            $lawPolicySource->name,
            'Jurisdiction',
            "{$lawPolicySource->municipality}, Ontario, Canada",
            'Year in Effect',
            $lawPolicySource->year_in_effect,
            'Reference',
            $lawPolicySource->reference,
            'Type',
            $lawPolicySource->type->value,
            'Effect on Legal Capacity',
            $lawPolicySource->is_core ? 'Core - directly affects legal capacity' : 'Supplemental - indirectly affects legal capacity',
            'Provisions',
        ];

        $urls = [
            "href=\"{$lawPolicySource->reference}\""
        ];

        foreach ($lawPolicySource->provisions as $provision) {
            $strings[] = "Section / Subsection: {$provision->section}";
            $strings[] = $provision['body'];
            $strings[] = "Section / Subsection: {$provision->section} Reference";
            $strings[] = 'Other Information';
            $strings[] = "{$provision->legal_capacity_approach->value} approach to legal capacity";
            $strings[] = "Recognizes {$provision->decision_making_capability->value} decision making capability";
            $strings[] = 'Legal Information';
            if ($provision->is_subject_to_challenge) {
                $strings[] = "This provision is, or has been, subject to a constitutional or other court challenge.";
            }
            if ($provision->is_result_of_challenge) {
                $strings[] = "This provision is the result of a court challenge.";
            }
            $decision_types = implode(', ', $provision->decision_type);
            $strings[] = "Type of Decision: {$decision_types}";
            $strings[] = "Decision Citation: {$provision->decision_citation}";

            $urls[] = "href=\"{$provision->reference}\"";
        }

        $view = $this->view('law-policy-sources.show', ['lawPolicySource' => $lawPolicySource]);
        $view->assertSeeTextInOrder($strings);
        $view->assertSeeInOrder($urls, false);
    }

    /**
     *
     * @return void
     */
    public function test_a_view_with_minimum_fields_render()
    {
        // create a Law and Policy Source to use for the test
        $lawPolicySource = LawPolicySource::factory()
            ->create([
                'type' => null,
                'is_core' => null,
                'reference' => null,
                'jurisdiction' => "CA-ON",
                'municipality' => null
            ]);

        $removed_strings = [
            'Reference',
            'Type',
            'Effect on Legal Capacity',
            'Provisions'
        ];

        $strings = [
            $lawPolicySource->name,
            'Jurisdiction',
            'Ontario, Canada',
            'Year in Effect',
            $lawPolicySource->year_in_effect
        ];

        $view = $this->view('law-policy-sources.show', ['lawPolicySource' => $lawPolicySource]);
        $view->assertSeeTextInOrder($strings);
        foreach ($removed_strings as $removed_string) {
            $view->assertDontSeeText($removed_string);
        }
    }

    /**
     *
     * @return void
     */
    public function test_a_view_with_minimum_provision_fields_render()
    {
        // create a Law and Policy Source to use for the test
        $lawPolicySource = LawPolicySource::factory()
            ->create([
                'type' => null,
                'is_core' => null,
                'reference' => null,
                'jurisdiction' => "CA-ON",
                'municipality' => null
            ]);

        Provision::factory(3)
            ->for($lawPolicySource)
            ->create([
                'decision_type' => null,
                'legal_capacity_approach' => null,
                'decision_making_capability' => null,
                'reference' => null,
                'is_subject_to_challenge' => null,
                'is_result_of_challenge' => null,
                'decision_citation' => null,
            ]);

        $removed_strings = [
            'Reference',
            'Type',
            'Effect on Legal Capacity',
            'Other Information',
            'Legal Information'
        ];

        $strings = [
            $lawPolicySource->name,
            'Jurisdiction',
            'Ontario, Canada',
            'Year in Effect',
            $lawPolicySource->year_in_effect,
            'Provisions'
        ];

        foreach ($lawPolicySource->provisions as $provision) {
            $strings[] = "Section / Subsection: {$provision->section}";
            $strings[] = $provision['body'];
            $removed_strings[] = "Section / Subsection: {$provision->section} Reference";
        }

        $view = $this->view('law-policy-sources.show', ['lawPolicySource' => $lawPolicySource]);
        $view->assertSeeTextInOrder($strings);
        foreach ($removed_strings as $removed_string) {
            $view->assertDontSeeText($removed_string);
        }
    }
}
