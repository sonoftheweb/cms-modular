<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\SanitizeRequestHelper;
use App\Http\Controllers\Controller;
use App\Models\Instance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    /**
     * Registers a user and creates an account
     *
     * @param Request $request
     * @return Response
     */
    public function registerInstance(Request $request)
    {
        $request = SanitizeRequestHelper::sanitizeInput($request); // @todo move this into a middleware

        $validator = Validator::make($request->all(), [
            'instance_name' => 'required|max:20',
            'name' => 'required|max:20',
            'email' => 'required|unique:users,email|email:rfc,dns',
            'password' => 'required|min:6|max:20',
            'password_confirmation' => 'required'
        ]);

        if ($validator->fails())
            return response([
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 401);

        // Create the instance
        $instance = Instance::create([
            'instance_name' => $request->instance_name,
            'direct_email' => $request->email,
        ]);

        // Create the user
        $user = $instance->users()->create([
            'role_id' => Config::get('constants.roles.account_manager'),
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->attribute()->create([]);

        // update instance with the new account info
        $instance->account_manager_user_id = $user->id;
        $instance->save();

        $instance->createAsStripeCustomer([
            'email' => $instance->direct_email,
            'name' => $instance->instance_name
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response([
            'token' => $token
        ]);
    }

    public function login(Request $request)
    {
        if (Auth::attempt($request->except('remember_me'), $request->remember_me)) {
            $user = Auth::user();
             return response([
                 'token' => $user->createToken('auth-token')->plainTextToken
             ]);
        } else {
            return response([
                'message' => 'User not found'
            ], 401);
        }
    }
}
