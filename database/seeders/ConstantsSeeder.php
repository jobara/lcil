<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ConstantsSeeder extends Seeder
{
    /**
     * Seed the application's database with constant values that should be available in all instances.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            ConstantMeasureSeeder::class,
        ]);
    }
}
