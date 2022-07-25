<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ConstantsSeeder::class,
            UserSeeder::class,
            RegimeAssessmentSeeder::class,
            // ProvisionSeeder::class,
        ]);
    }
}
