<?php

namespace Database\Seeders;

use App\Models\Measure;
use Illuminate\Database\Seeder;

class MeasureSeeder extends Seeder
{
    public function run(): void
    {
        Measure::factory(10)->create();
    }
}
