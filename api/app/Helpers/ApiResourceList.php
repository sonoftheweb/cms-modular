<?php


namespace App\Helpers;


class ApiResourceList
{
    public static function resourceList()
    {
        return [
            'users' => [
                'model' => '\App\Models\User',
                'dependencies' => '\App\Http\Resources\User\UserDependencies',
                'resource' => '\App\Http\Resources\User\UserResource',
                'collection' => '\App\Http\Resources\User\UserCollection',
            ],
            'roles' => [
                'model' => '\App\Models\Role',
                'resource' => '\App\Http\Resources\RoleResource',
                'collection' => '\App\Http\Resources\Collections\RoleCollection',
            ]
        ];
    }

    public static function getResourceDefinition($resource)
    {
        return self::resourceList()[$resource];
    }
}
