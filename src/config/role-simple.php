<?php

/**
 * @license MIT
 * @package WalkerChiu\RoleSimple
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Switch association of package to On or Off
    |--------------------------------------------------------------------------
    |
    | When you set someone On:
    |     1. Its Foreign Key Constraints will be created together with data table.
    |     2. You may need to change the corresponding class settings in the config/wk-core.php.
    |
    | When you set someone Off:
    |     1. Association check will not be performed on FormRequest and Observer.
    |     2. Cleaner and Initializer will not handle tasks related to it.
    |
    | Note:
    |     The association still exists, which means you can still access related objects.
    |
    */
    'onoff' => [
        'user' => 1,

        'group'          => 0,
        'morph-category' => 0,
        'morph-image'    => 0,
        'rule'           => 0,
        'rule-hit'       => 0,
        'site'           => 0
    ],

    /*
    |--------------------------------------------------------------------------
    | Command
    |--------------------------------------------------------------------------
    |
    | Location of Commands.
    |
    */
    'command' => [
        'cleaner' => 'WalkerChiu\RoleSimple\Console\Commands\RoleSimpleCleaner'
    ],

    /*
    |--------------------------------------------------------------------------
    | Redirect the unauthorized user to the specified route.
    |--------------------------------------------------------------------------
    |
    | Route Name.
    |
    */
    'redirect' => [
        'role'       => 'admin.login',
        'permission' => 'admin.login'
    ]
];
