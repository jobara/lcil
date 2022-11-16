<?php

namespace Database\Seeders;

use App\Models\Provision;
use Illuminate\Database\Seeder;

class ProvisionSeeder extends Seeder
{
    public function run(): void
    {
        Provision::factory(25)->create();
    }
}
