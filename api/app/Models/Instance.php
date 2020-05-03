<?php

namespace App\Models;

use App\Models\Traits\HasUsersTrait;
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
}
