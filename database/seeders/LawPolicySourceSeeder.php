<?php

namespace Database\Seeders;

use App\Models\LawPolicySource;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LawPolicySourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        LawPolicySource::factory(20)->create();
    }
}
