<?php


namespace App\Models\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Config;

trait HasUsersTrait
{
    public function users()
    {
        return $this->hasMany(User::class, 'instance_id');
    }
    public function adminUsers()
    {
        return $this->users()->where('role_id', Config::get('constants.roles.account_manager'));
    }
}
