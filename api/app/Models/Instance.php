<?php

namespace App\Models;

use App\Models\Traits\HasUsersTrait;
use Carbon\Carbon;
use Laravel\Cashier\Billable;

class Instance extends SimpleModel
{
    use HasUsersTrait, Billable;

    protected $fillable = [
        'instance_name',
        'account_manager_user_id',
        'logo',
        'seats',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'country',
        'website',
        'direct_phone',
        'direct_email'
    ];

    public function taxRates()
    {
        return ['txr_1GgR9nCS6ut62cx8XLcBLYZy'];
    }

    public function getSubscriptions()
    {
        return $this->asStripeCustomer()->subscriptions->data;
    }

    public function getSubscription()
    {
        $subscriptions = $this->getSubscriptions();
        $subscription = array_filter($subscriptions, function ($sub) {
            return $sub->status === 'active';
        });

        return $subscription[0];
    }

    public function discount()
    {
        return $this->getSubscription()->discount;
    }

    public function discountCustomerId()
    {
        return $this->discount()->customer;
    }

    public function discountSubscriptionId()
    {
        return $this->discount()->subscription;
    }

    public function discountStartDate()
    {
        return Carbon::createFromTimestamp($this->discount()->start);
    }

    public function discountEndDate()
    {
        return Carbon::createFromTimestamp($this->discount()->end);
    }

    public function discountDaysLeft()
    {
        return $this->discountEndDate()
            ->diffInDays($this->discountStartDate());
    }

    public function coupon()
    {
        return $this->discount()->coupon;
    }

    public function couponIsValid()
    {
        return $this->coupon()->valid;
    }
}
