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
            'name' => 'required|min:1|max:20',
            'email' => 'required|unique:users,email|email:rfc,dns',
            'password' => 'required|min:6|max:20',
            'confPassword' => 'required|min:6|max:20',
            'instance.instance_name' => 'required|min:2|max:250',
            'instance.address_line_1' => 'required|min:5|max:250',
            'instance.address_line_2' => 'max:250',
            'instance.city' => 'required|min:5|max:250',
            'instance.state' => 'required|min:5|max:250',
            'instance.country' => 'required|min:5|max:250',
            'instance.website' => 'max:250',
            'instance.direct_phone' => 'max:17',
            'instance.direct_email' => 'required|min:5|max:250',
            'user_attributes.user_job_title' => 'max:250',
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
        $instance = Instance::create([
            'instance_name' => $request->instance['instance_name'],
            'address_line_1' => $request->instance['address_line_1'],
            'address_line_2' => $request->instance['address_line_2'],
            'city' => $request->instance['city'],
            'state' => $request->instance['state'],
            'country' => $request->instance['country'],
            'website' => $request->instance['website'],
            'direct_phone' => $request->instance['direct_phone'],
            'direct_email' => $request->instance['direct_email']
        ]);

        // Create the user
        $user = $instance->users()->create([
            'role_id' => Config::get('constants.roles.account_manager'),
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->attribute()->create([
            'user_job_title' => $request->user_attributes['user_job_title']
        ]);

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
        if ($request->has('confirm_password') && !empty($request->confirm_password)) {
            // check that passwords match else abort
            if ($request->password !== $request->confirm_password)
                return response([
                    'status' => 'action_required',
                    'message' => 'The password confirmation did not pass validation. Please re-enter the password again.'
                ]);

            User::where('email', $request->email)->update([
                'password' => Hash::make($request->password)
            ]);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'active' => true], $request->remember_me)) {
            $user = Auth::user();
             return response([
                 'token' => $user->createToken('auth-token')->accessToken
             ]);
        } else {

            // check if the user exist and has a password
            $user = User::where('email', $request->email)->first();
            if (empty($user->password)) {
                return response([
                    'status' => 'action_required',
                    'message' => 'This account does not have a password as it was recently created by another admin. If you wish to use the same password you entered in the login form, please confirm that password here, else try again.'
                ]);
            }

            return response([
                'message' => 'User not found'
            ], 401);
        }
    }

    public function logout()
    {
        if (!Auth::check())
            return response(['message' => 'Access denied for this operation.'], 404);

        DB::table('oauth_access_tokens')->where('user_id', Auth::id())->delete();
        Session::flush();
        return response([
            'message' => 'User logged out.'
        ]);
    }
}
