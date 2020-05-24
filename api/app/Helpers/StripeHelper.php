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
                'product_id' => $plan['product'],
                'tiers' => ($forDb) ? json_encode($plan['tiers'], true) : $plan['tiers'],
                'trial_period_days' => ($plan['trial_period_days']) ? $plan['trial_period_days'] : 14
            ];
        }, $plans['data']);

        return $plans;
    }

    public static function instanceHasPlan ()
    {
        $plans = \App\Models\Plan::all()->toArray();
        $plans = array_map(function ($plan) {
            return $plan['stripe_plan_identifier'];
        }, $plans);

        return InstanceHelper::getInstance()->subscribedToPlan($plans, 'default');
    }

    public static function toStripeAmountFormat(float $priceInDollars): int
    {
        if ($priceInDollars) {
            return $priceInDollars * 100;
        }
        return 0;
    }
}
