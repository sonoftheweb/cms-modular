<?php

namespace App\Http\Controllers;

use App\Helpers\InstanceHelper;
use App\Helpers\SearchSortPaginateHelper;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\RoleCollection;
use App\Http\Resources\UserCollection;
use App\Models\Role;
use App\Models\UserAsModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;

class UserController extends ApiController
{
    /**
     * Get the current user data
     *
     * @param Request $request Request data
     *
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

    public function roles(Request $request)
    {
        return $request->response_helper->respond(
            new RoleCollection(Role::all())
        );
    }

    /**
     * Gets all users in a specific instance
     *
     * @param Request $request
     * @return UserCollection
     */
    public function index(Request $request)
    {
        $users = UserAsModel::with('role', 'attribute');
        $users = SearchSortPaginateHelper::searchSortAndPaginate($request, $users);
        return new UserCollection($users);
    }

    public function update(Request $request, $id)
    {
        $user = UserAsModel::where('id', $id)->first();
        $user->update([
            'name' => $request->name,
            'active' => $request->active
        ]);
        $user->attribute()->update($request->attribute);

        return $request->response_helper->respondWithSuccessMessage(200, 'Successfully updated user.');
    }

    public function store(Request $request)
    {
        $user = new UserAsModel;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role_id = $request->role;
        $user->save();

        $user->attribute()->create($request->attribute);

        return $request->response_helper->respondWithSuccessMessage(200, 'Successfully updated user.');
    }

    public function destroy(Request $request, $id)
    {
        User::where('id', $id)->delete();
        return $request->response_helper->respondWithSuccessMessage(200, 'Successfully deleted user.');
    }
}
