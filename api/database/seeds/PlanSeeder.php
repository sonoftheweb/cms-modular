<?php

use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $plans = \App\Helpers\StripeHelper::getPlans(true);
        foreach ($plans as $plan) {
            \App\Models\Plan::create($plan);
        }
    }
}
