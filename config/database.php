<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
        ],

        'sqlite_testing' => [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ],

        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
            'options'   => [
                PDO::ATTR_PERSISTENT => true,
            ],
        ],

        'login' => [
            'driver' => 'mysql',
            'host' => env('LOGIN_DB_HOST', '127.0.0.1'),
            'port' => env('LOGIN_DB_PORT', '3306'),
            'database' => env('LOGIN_DB_DATABASE', 'forge'),
            'username' => env('LOGIN_DB_USERNAME', 'forge'),
            'password' => env('LOGIN_DB_PASSWORD', ''),
            'unix_socket' => env('LOGIN_DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'messenger' => [
            'driver' => 'mysql',
            'host' => env('CLASSMONITOR_DB_HOST', '127.0.0.1'),
            'port' => env('CLASSMONITOR_DB_PORT', '3306'),
            'database' => env('CLASSMONITOR_DB_DATABASE', 'forge'),
            'username' => env('CLASSMONITOR_DB_USERNAME', 'forge'),
            'password' => env('CLASSMONITOR_DB_PASSWORD', ''),
            'unix_socket' => env('CLASSMONITOR_DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'learning' => [
            'driver' => 'mysql',
            'host' => env('LEARNING_DB_HOST', '127.0.0.1'),
            'port' => env('LEARNING_DB_PORT', '3306'),
            'database' => env('LEARNING_DB_DATABASE', 'forge'),
            'username' => env('LEARNING_DB_USERNAME', 'forge'),
            'password' => env('LEARNING_DB_PASSWORD', ''),
            'unix_socket' => env('LEARNING_DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'community' => [
            'driver' => 'mysql',
            'host' => env('COMMUNITY_DB_HOST', '127.0.0.1'),
            'port' => env('COMMUNITY_DB_PORT', '3306'),
            'database' => env('COMMUNITY_DB_DATABASE', 'forge'),
            'username' => env('COMMUNITY_DB_USERNAME', 'forge'),
            'password' => env('COMMUNITY_DB_PASSWORD', ''),
            'unix_socket' => env('COMMUNITY_DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => 'predis',

        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 0,
        ],

    ],

];
