<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;

class RegistrationRequest extends BaseReuest
{
    /**
     * Authorize if the user us a guest
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::guest();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'instance_name' => 'required|max:20',
            'name' => 'required|max:10',
            'email' => 'required|email:rfc,dns',
            'password' => 'required|min:6|max:20',
            'password_confirmation' => 'required'
        ];
    }
}
