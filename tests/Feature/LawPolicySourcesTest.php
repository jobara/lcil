<?php

namespace Tests\Feature;

use App\Enums\LawPolicyTypeEnum;
use App\Enums\LegalChallengeTypeEnum;
use App\Models\LawPolicySource;
use App\Models\Provision;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LawPolicySourcesTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_law_policy_sources_show_route_responded_successfully()
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
     * Verify the measures view can renderer properly
     *
     * @return void
     */
    public function test_a_law_policy_source_with_all_fields_view_can_be_rendered()
    {
        // create a Law and Policy Source to use for the test
        $lawPolicySource = LawPolicySource::factory()
            ->create([
                'type' => $this->faker->randomElement(array_column(LawPolicyTypeEnum::cases(), 'value')),
                'is_core' => $this->faker->boolean(),
                'reference' => $this->faker->unique()->url(),
                'jurisdiction' => "CA-ON",
                'municipality' => ucfirst($this->faker->word()),
            ]);

        Provision::factory(3)
            ->for($lawPolicySource)
            ->create([
                'type_of_decision' => $this->faker->randomElement(array_column(LegalChallengeTypeEnum::cases(), 'value')),
                'reference' => $this->faker->unique()->url(),
                'is_subject_to_challenge' => true,
                'is_result_of_challenge' => true,
                'decision_citation' => $this->faker->paragraph(),
            ]);

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

        foreach ($lawPolicySource->provisions() as $provision) {
            $strings[] = "Section / Subsection: {$provision['section']}";
            $strings[] = $provision['body'];
            $strings[] = 'Type of decision';
            $strings[] = $provision['type_of_decision']->value; $strings[] = 'This provision is, or has been, subject to a constitutional or other court challenge.';
            $strings[] = 'This provision is the result of a court challenge.';
            $strings[] = 'Decision Citation';
            $strings[] = $provision['decision_citation'];
        }

        $view = $this->view('law-policy-sources.show', ['lawPolicySource' => $lawPolicySource]);
        $view->assertSeeTextInOrder($strings);
    }
}
