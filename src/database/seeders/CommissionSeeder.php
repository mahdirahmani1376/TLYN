<?php

namespace Database\Seeders;

use App\Models\Commission;
use Illuminate\Database\Seeder;

class CommissionSeeder extends Seeder
{
    public function run(): void
    {
        Commission::create([
            'min_amount' => 0,
            'max_amount' => 1,
            'rate' => 2
        ]);

        Commission::create([
            'min_amount' => 1,
            'max_amount' => 10,
            'rate' => 1.5
        ]);

        Commission::create([
            'min_amount' => 10,
            'max_amount' => null,
            'rate' => 1
        ]);
    }
}
