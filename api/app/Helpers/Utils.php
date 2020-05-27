<?php


namespace App\Helpers;


class Utils
{
    public static function is_valid_json($value):bool
    {
        if (gettype($value) !== 'string')
            return false;

        json_decode($value, true);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
