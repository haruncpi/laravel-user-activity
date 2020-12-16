<?php

return [
    'middleware'       => ['web', 'admin'],
    'route_path'       => 'admin/user-activity',
    'admin_panel_path' => 'en/admin',
    'delete_limit'     => 7,

    /*
     * for multi auth you can add your guard like admin 
     * the default guard is user
     */
    'admin_guard'      => '',
    /*
     * for multi auth you can add your model to refer the user
     * the default model is  \App\User::class
     */
    'model'            => \App\Admin::class,

    'log_events' => [
        'on_edit'    => true,
        'on_delete'  => true,
        'on_login'   => true,
        'on_lockout' => true
    ]
];
