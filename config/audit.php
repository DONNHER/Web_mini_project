<?php

return [
    'enabled' => env('AUDITING_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Audit Implementation
    |--------------------------------------------------------------------------
    |
    | The Audit implementation to use.
    |
    */

    'implementation' => App\Models\Audit::class,

    /*
    |--------------------------------------------------------------------------
    | User
    |--------------------------------------------------------------------------
    |
    | The User model configuration.
    |
    */

    'user' => [
        'morph_prefix' => 'user',
        'guards' => [
            'web',
            'api',
        ],
        'resolver' => OwenIt\Auditing\Resolvers\UserResolver::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Resolvers
    |--------------------------------------------------------------------------
    |
    | The IP Address, User Agent and URL resolvers.
    |
    */

    'resolvers' => [
        'ip_address' => OwenIt\Auditing\Resolvers\IpAddressResolver::class,
        'user_agent' => OwenIt\Auditing\Resolvers\UserAgentResolver::class,
        'url'        => OwenIt\Auditing\Resolvers\UrlResolver::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    |
    | The events that should be audited.
    |
    */

    'events' => [
        'created',
        'updated',
        'deleted',
        'restored',
    ],

    /*
    |--------------------------------------------------------------------------
    | Strict Mode
    |--------------------------------------------------------------------------
    |
    | When strict mode is enabled, an Audit is only created if there are
    | actually changes in the Auditable model.
    |
    */

    'strict' => false,

    'audit_console' => true,

    'queue' => [
        'enable' => true,
        'connection' => env('QUEUE_CONNECTION', 'sync'),
        'queue' => 'default',
        'delay' => 0,
    ],

    'console' => [
        'enabled' => env('AUDIT_CONSOLE_ENABLED', true),
    ],
];
