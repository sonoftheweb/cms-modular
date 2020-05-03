<?php


namespace App\Helpers;


use Illuminate\Support\Facades\Cache;
use Stripe\Plan;
use Stripe\Stripe;

class StripeHelper
{
    protected $filter = [
        'id',
        'nickname',
        'object',
        'active',
        'currency',
        'interval',
        'interval_count',
        'livemode',
        'metadata',
        'product',
        'tiers',
        'trial_period_days',
    ];

    public static function stripeConnect() :void
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    public static function getPlans (bool $forDb = false) :array
    {
        StripeHelper::stripeConnect();
        $plans = Plan::all([
            'product' => env('STRIPE_MAIN_PRODUCT'),
            'active' => true
        ]);

        $plans = $plans->toArray();
        $plans = array_map(function ($plan) use ($forDb) {
            return [
                'stripe_plan_identifier' => $plan['id'],
                'nickname' => $plan['nickname'],
                'object' => $plan['object'],
                'active' => $plan['active'],
                'currency' => $plan['currency'],
                'interval' => $plan['interval'],
                'interval_count' => $plan['interval_count'],
                'livemode' => $plan['livemode'],
                'metadata' => ($forDb) ? json_encode($plan['metadata'], true) : $plan['metadata'],
                'product' => $plan['product'],
                'tiers' => ($forDb) ? json_encode($plan['tiers'], true) : $plan['tiers'],
                'trial_period_days' => $plan['trial_period_days']
            ];
        }, $plans['data']);

        return $plans;
    }

    public static function instanceHasPlan ()
    {
        $instance = InstanceHelper::getInstance();
        $plans = Cache::remember('all_plans_in_db', env('CACHE_TIME_ONE_HOUR'), function () {
            $p = \App\Models\Plan::select('stripe_plan_identifier')->get()->toArray();
            return array_values($p);
        });

        return $instance->subscribedToPlan($plans, 'default');
    }

    public static function toStripeAmountFormat(float $priceInDollars): int
    {
        if ($priceInDollars) {
            return $priceInDollars * 100;
        }
        return 0;
    }
}
