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

        $instance = Cache::remember(
            'user_'. $user->id . '_' . $user->instance_id,
            env('CACHE_TIME_ONE_DAY'),
            function () use ($user) {
                return $user->instance()->first();
            }
        );

        return static::$instance = $instance;
    }
}
