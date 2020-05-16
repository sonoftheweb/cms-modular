<?php


namespace App\Models\Traits;


trait UserModelTraits
{
    public function attribute()
    {
        return $this->hasOne('App\Models\UserAttribute', 'user_id', 'id');
    }

    public function role()
    {
        return $this->belongsTo('App\Models\Role', 'role_id', 'id');
    }
}
