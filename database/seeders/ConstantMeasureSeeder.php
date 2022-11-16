<?php

namespace Database\Seeders;

use App\Models\Measure;
use App\Models\MeasureDimension;
use App\Models\MeasureIndicator;
use Illuminate\Database\Seeder;

class ConstantMeasureSeeder extends Seeder
{
    public function run(): void
    {
        // Don't seed if the database tables are already populated
        if (MeasureDimension::count()) {
            return;
        }

        if (MeasureIndicator::count()) {
            return;
        }

        if (Measure::count()) {
            return;
        }

        $json = file_get_contents('database/data/measures.json');
        $dimensions = json_decode($json, true);

        foreach ($dimensions as $dimension) {
            $measureDimension = MeasureDimension::factory()
                ->create([
                    'code' => $dimension['code'],
                    'description' => $dimension['description'],
                ]);

            foreach ($dimension['indicators'] as $indicator) {
                $measureIndicator = MeasureIndicator::factory()
                    ->for($measureDimension, 'dimension')
                    ->create([
                        'code' => $indicator['code'],
                        'description' => $indicator['description'],
                    ]);

                foreach ($indicator['measures'] as $measure) {
                    Measure::factory()
                        ->for($measureIndicator, 'indicator')
                        ->create($measure);
                }
            }
        }
    }
}
