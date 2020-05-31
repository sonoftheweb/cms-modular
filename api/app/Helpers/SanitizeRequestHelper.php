<?php


namespace App\Helpers;


use Illuminate\Http\Request;

class SanitizeRequestHelper
{
    public static function sanitizeInput(Request $request)
    {
        $input = $request->all();

        $args = [];
        foreach ($input as $k => $v) {
            if (is_array($v)) {
                $args[$k] = [
                    'filter' => FILTER_VALIDATE_INT,
                    'flags'  => FILTER_FORCE_ARRAY,
                ];
            }

            if (is_string($v)) {
                $args[$k] = FILTER_SANITIZE_ENCODED;
            }
        }

        filter_var_array($input, $args);

        $request->replace($input);
        return $request;
    }
}
