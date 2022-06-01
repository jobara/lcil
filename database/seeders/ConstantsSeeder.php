<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ConstantsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ConstantMeasureSeeder::class,
        ]);
    }
}
