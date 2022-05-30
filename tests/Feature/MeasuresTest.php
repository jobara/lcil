<?php

namespace Tests\Feature;

use App\Models\Measure;
use App\Models\MeasureDimension;
use App\Models\MeasureIndicator;
use Database\Seeders\ConstantMeasureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeasuresTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Run a specific seeder before each test.
     *
     * @var string
     */
    protected $seeder = ConstantMeasureSeeder::class;

    /**
     * Verify that the index route exists and responds correctly
     *
     * @return void
     */
    public function test_index_route()
    {
        $response = $this->get(localized_route('measures'));

        $response->assertStatus(200);
        $response->assertViewIs('measures.index');
        $response->assertViewHas('lcilMeasures');

        foreach ($response['lcilMeasures'] as $dimension) {
            $this->assertInstanceOf(MeasureDimension::class, $dimension);

            foreach ($dimension->indicators as $indicator) {
                $this->assertInstanceOf(MeasureIndicator::class, $indicator);

                foreach ($indicator->measures as $measure) {
                    $this->assertInstanceOf(Measure::class, $measure);
                }
            }
        }
    }

    /**
     * Verify the measures view can renderer properly
     *
     * @return void
     */
    public function test_the_measure_view_render()
    {
        $lcilMeasures = MeasureDimension::factory(2)
                            ->has(MeasureIndicator::factory(2)->has(Measure::factory(2), 'measures'), 'indicators')
                            ->create();
        $strings = [];

        foreach ($lcilMeasures as $dimension) {
            $strings[] = $dimension['code'];
            $strings[] = $dimension['description'];

            foreach ($dimension->indicators as $indicator) {
                $strings[] = $indicator['code'];
                $strings[] = $indicator['description'];

                foreach ($indicator->measures as $measure) {
                    $strings[] = $measure['code'];
                    $strings[] = $measure['title'];
                    $strings[] = $measure['type'];
                    $strings[] = $measure['description'];
                }
            }
        }

        $this->assertCount(44, $strings); // ensures that correct number of strings were found from $lcilMeasures
        $view = $this->view('measures.index', ['lcilMeasures' => $lcilMeasures]);
        $view->assertSeeTextInOrder($strings);
    }

    /**
     * Verify the MeasureDimension, MeasureIndicator and Measure model relationships.
     *
     * @return void
     */
    public function test_the_measure_model_relationships_are_established()
    {
        $dimension = MeasureDimension::factory()->create();
        $indicator = MeasureIndicator::factory()->for($dimension, 'dimension')->create();
        $measure = Measure::factory()->for($indicator, 'indicator')->create();

        // Dimension relationships
        $this->assertCount(1, $dimension->indicators);
        $this->assertEquals($indicator->code, $dimension->indicators[0]->code);

        // Indicator relationships
        $this->assertCount(1, $indicator->measures);
        $this->assertEquals($dimension->code, $indicator->dimension->code);
        $this->assertEquals($measure->code, $indicator->measures[0]->code);

        // Measure relationships
        $this->assertEquals($indicator->code, $measure->indicator->code);
    }
}
