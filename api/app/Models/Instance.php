<?php

namespace App\Models;

class Instance extends SimpleModel
{
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

    public function users()
    {
        return $this->hasMany(User::class, 'instance_id');
    }
}
