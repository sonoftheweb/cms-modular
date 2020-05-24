<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $table = 'plans';
    protected $fillable = [
        'stripe_plan_identifier',
        'nickname',
        'object',
        'active',
        'currency',
        'interval',
        'interval_count',
        'livemode',
        'metadata',
        'product_id',
        'tiers',
        'trial_period_days',
    ];

    protected $casts = [
        'tiers' => 'array',
        'metadata' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'stripe_product_identifier');
    }
}
