<?php


namespace App\Helpers;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class InstanceHelper
{
    static $instance = null;

    public static function getInstanceId()
    {
        $instance = self::getInstance();
        if (!$instance) {
            abort(404);
        }
        return $instance->id;
    }

    public static function getInstance()
    {
        $user = Auth::user();
        return static::$instance = $user->instance()->first();
    }
}
