<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Instance;
use App\Http\Controllers\Controller;
use App\Helpers\SanitizeRequestHelper;


class AuthenticationController extends Controller
{
    /**
     * Registers a user and creates an account
     *
     * @param Request $request request data
     *
     * @return Response
     */
    public function registerInstance(Request $request)
    {
        $request = SanitizeRequestHelper::sanitizeInput($request); // @todo move this into a middleware

        $validateData = [
            'instance_name' => 'required|max:20',
            'name' => 'required|max:20',
            'email' => 'required|unique:users,email|email:rfc,dns',
            'password' => 'required|min:6|max:20',
            'password_confirmation' => 'required'
        ];
        $validator = Validator::make($request->all(), $validateData);

        if ($validator->fails()) {
            $resp = [
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ];
            return response($resp, 401);
        }

        // Create the instance
        $instanceCreate = [
            'instance_name' => $request->instance_name,
            'direct_email' => $request->email
        ];
        $instance = Instance::create($instanceCreate);

        // Create the user
        $userCreate = [
            'role_id' => Config::get('constants.roles.account_manager'),
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ];
        $user = $instance->users()->create($userCreate);

        $user->attribute()->create([]);

        // update instance with the new account info
        $instance->account_manager_user_id = $user->id;
        $instance->save();

        $instance->createAsStripeCustomer([
            'email' => $instance->direct_email,
            'name' => $instance->instance_name
        ]);

        $token = $user->createToken('auth-token')->accessToken;

        return response([
            'token' => $token
        ]);
    }

    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'active' => true], $request->remember_me)) {
            $user = Auth::user();
             return response([
                 'token' => $user->createToken('auth-token')->accessToken
             ]);
        } else {
            return response([
                'message' => 'User not found'
            ], 401);
        }
    }

    public function logout()
    {
        if (!Auth::check())
            return response(['message' => 'Access denied for this operation.'], 404);

        DB::table('oauth_access_token')->where('user_id', Auth::id())->delete();
        Session::flush();
        return response([
            'message' => 'User logged out.'
        ]);
    }
}
