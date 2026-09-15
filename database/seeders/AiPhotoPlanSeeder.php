<?php

namespace Database\Seeders;

use App\Models\AiPhotoPlan;
use Illuminate\Database\Seeder;

class AiPhotoPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            ['name' => '10 Photos', 'credits' => 10, 'price' => 2.00, 'sort_order' => 1],
            ['name' => '20 Photos', 'credits' => 20, 'price' => 3.50, 'sort_order' => 2],
            ['name' => '50 Photos', 'credits' => 50, 'price' => 7.00, 'sort_order' => 3],
        ];

        foreach ($plans as $plan) {
            AiPhotoPlan::updateOrCreate(
                ['credits' => $plan['credits']],
                array_merge($plan, ['currency' => 'usd', 'is_active' => true])
            );
        }
    }
}
