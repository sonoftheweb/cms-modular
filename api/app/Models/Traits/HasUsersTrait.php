<?php


namespace App\Models\Traits;

use App\Models\User;

trait HasUsersTrait
{
    public function users()
    {
        return $this->hasMany(User::class, 'instance_id');
    }
}
