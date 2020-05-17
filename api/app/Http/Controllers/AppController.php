<?php

namespace App\Http\Controllers;


class AppController extends Controller
{
    public function app()
    {
        $data = [
            'is_authenticated' => false,
            'session_lifetime' => env('SESSION_LIFETIME')
        ];

        if (env('APP_DEBUG') == true) {
            $data['debug'] = true;
        }

        if (env('APP_ENV') !== 'production') {
            $data['not_production'] = true;
        }

        return response($data, 200);
    }
}
