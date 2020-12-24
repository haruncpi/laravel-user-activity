<?php

return [
    'middleware'       => ['web', 'auth'],
    'route_path'       => 'admin/user-activity',
    'admin_panel_path' => 'admin/dashboard',
    'delete_limit'     => 7,

    'model' => [
        'user' => "App\User"
    ],

    'log_events' => [
        'on_create'     => false,
        'on_edit'       => true,
        'on_delete'     => true,
        'on_login'      => true,
        'on_lockout'    => true
    ]
];
