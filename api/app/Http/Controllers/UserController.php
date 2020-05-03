<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Get the current user data
     * @param Request $request
     * @return mixed
     */
    public function me(Request $request)
    {
        if ($request->method() === 'POST') {
            list($user, $userData) = [
                User::findWhere('id', Auth::id()),
                ['name' => $request->name]
            ];

            if (Hash::check($request->old_password, $user->password)) {
                $userData['password'] = Hash::make($request->new_password);
            }

            $user->fill($userData)->save();
        }

        return $request->response_helper->respond(
            new UserResource(
                User::firstWhere('id', Auth::id())->load(['role', 'attribute', 'instance'])
            )
        );
    }
}
