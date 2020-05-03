<?php

namespace App\Http\Middleware;

use App\Helpers\InstanceHelper;
use App\Helpers\StripeHelper;
use App\Models\Plan;
use Closure;

class SubscriptionCheck
{
    /**
     * Check if the instance has a subscription
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (StripeHelper::instanceHasPlan()) {
            return $next($request);
        }
        else {
            return response([
                'message' => 'This account has no subscription... Please subscribe to a plan now.',
                'error_type' => 'has_no_subscription'
            ], 404);
        }
    }
}
