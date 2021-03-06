<?php


namespace App\Helpers;


class SmartTableHeadersDefinitions
{
    public static function users()
    {
        return [
            [
                'text' => 'Name',
                'align' => 'start',
                'sortable' => true,
                'value' => 'name'
            ],
            [
                'text' => 'Email',
                'align' => 'start',
                'sortable' => true,
                'value' => 'email'
            ],
            [
                'text' => 'Status',
                'align' => 'start',
                'sortable' => true,
                'value' => 'status'
            ],
            [
                'text' => 'Role',
                'align' => 'start',
                'sortable' => true,
                'value' => 'role_name'
            ],
            [
                'text' => 'action',
                'align' => 'end',
                'sortable' => false,
                'value' => 'action'
            ],
        ];
    }
    public static function roles()
    {
        return [
            [
                'text' => 'Role name',
                'align' => 'start',
                'sortable' => true,
                'value' => 'role_name'
            ]
        ];
    }
}
