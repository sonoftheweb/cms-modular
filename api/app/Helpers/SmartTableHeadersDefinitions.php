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
}
