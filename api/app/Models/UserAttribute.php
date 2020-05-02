<?php

namespace App\Models;


class UserAttribute extends SimpleModel
{
    protected $table = 'users_attributes';
    protected $fillable = [
        'user_id',
        'user_job_title',
        'user_job_description',
        'profile_picture'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
