<?php

namespace Database\Seeders;

use App\Models\Evaluation;
use App\Models\LawPolicySource;
use App\Models\Measure;
use App\Models\Provision;
use App\Models\RegimeAssessment;
use Illuminate\Database\Seeder;

class RegimeAssessmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $measures = Measure::all();
        $regimeAssessments = RegimeAssessment::factory(5)->create();

        foreach ($regimeAssessments as $regimeAssessment) {
            $jurisdiction = $regimeAssessment->jurisdiction;
            $municipality = $regimeAssessment->municipality;

            $lawPolicySources = LawPolicySource::factory(5)
                ->has(Provision::factory(5))
                ->create([
                    'jurisdiction' => $jurisdiction,
                    'municipality' => $municipality,
                ]);

            $regimeAssessment->lawPolicySources()->attach($lawPolicySources->modelKeys());

            foreach ($lawPolicySources as $lawPolicySource) {
                foreach ($lawPolicySource->provisions as $provision) {
                    $measure = $measures->random();
                    Evaluation::factory()
                        ->for($regimeAssessment)
                        ->for($provision)
                        ->for($measure)
                        ->create();
                }
            }
        }
    }
}
