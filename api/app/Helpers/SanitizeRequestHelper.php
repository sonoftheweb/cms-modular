<?php


namespace App\Helpers;


use Illuminate\Http\Request;

class SanitizeRequestHelper
{
    public static function sanitizeInput(Request $request)
    {
        $input = $request->all();

        foreach ($input as $key => $value) {
            $input[$key] = filter_var(trim($value), FILTER_SANITIZE_STRING);
        }

        $request->replace($input);
        return $request;
    }
}
