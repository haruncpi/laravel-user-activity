<?php

return [
    'activated'        => true, // active/inactive all logging
    'middleware'       => ['web', 'auth'],
    'route_path'       => 'admin/user-activity',
    'admin_panel_path' => 'admin/dashboard',
    'delete_limit'     => 7, // default 7 days

    'model' => [
        'user' => "App\Model\User"
    ],

    'log_events' => [
        'on_create'     => false,
        'on_edit'       => true,
        'on_delete'     => true,
        'on_login'      => true,
        'on_lockout'    => true,
        'on_route'      => true,
    ],

    'ignore_routes' => [
        '*debugbar*'
    ],

    'ignore_urls' => [
        '*user-activity*'
    ],

    'log_response' => [
        'enabled' => true,
        'max_characters' => 200,
    ]
];
