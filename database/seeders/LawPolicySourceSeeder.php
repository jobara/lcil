<?php

namespace Database\Seeders;

use App\Models\LawPolicySource;
use Illuminate\Database\Seeder;

class LawPolicySourceSeeder extends Seeder
{
    public function run(): void
    {
        LawPolicySource::factory(20)->create();
    }
}
