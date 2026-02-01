<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for database operations. This is
    | the connection which will be utilized unless another connection
    | is explicitly specified when you execute a query / statement.
    |
    */

    'default' => env('DB_CONNECTION', 'laravel'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Below are all of the database connections defined for your application.
    | An example configuration is provided for each database system which
    | is supported by Laravel. You're free to add / remove connections.
    |
    */

    'connections' => [
        'laravel' => [
            'driver' => env('LARAVEL_DB_DRIVER', 'pgsql'),
            'url' => env('LARAVEL_DB_URL'),
            'host' => env('LARAVEL_DB_HOST', '127.0.0.1'),
            'port' => env('LARAVEL_DB_PORT', '5432'),
            'database' => env('LARAVEL_DB_DATABASE', 'scf'),
            'username' => env('LARAVEL_DB_USERNAME', 'root'),
            'password' => env('LARAVEL_DB_PASSWORD', ''),
            'charset' => env('LARAVEL_DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => env('LARAVEL_DB_SCHEMA', 'public'),
            'sslmode' => 'prefer',
        ],
        'my' => [
            'driver' => env('MY_DB_DRIVER', 'pgsql'),
            'url' => env('MY_DB_URL'),
            'host' => env('MY_DB_HOST', '127.0.0.1'),
            'port' => env('MY_DB_PORT', '5432'),
            'database' => env('MY_DB_DATABASE', 'scf'),
            'username' => env('MY_DB_USERNAME', 'root'),
            'password' => env('MY_DB_PASSWORD', ''),
            'charset' => env('MY_DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => env('MY_DB_SCHEMA', 'my'),
            'sslmode' => 'prefer',
        ],
        'manage' => [
            'driver' => env('MANAGE_DB_DRIVER', 'pgsql'),
            'url' => env('MANAGE_DB_URL'),
            'host' => env('MANAGE_DB_HOST', '127.0.0.1'),
            'port' => env('MANAGE_DB_PORT', '5432'),
            'database' => env('MANAGE_DB_DATABASE', 'scf'),
            'username' => env('MANAGE_DB_USERNAME', 'root'),
            'password' => env('MANAGE_DB_PASSWORD', ''),
            'charset' => env('MANAGE_DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => env('MANAGE_DB_SCHEMA', 'manage'),
            'sslmode' => 'prefer',
        ],
        'admin' => [
            'driver' => env('ADMIN_DB_DRIVER', 'pgsql'),
            'url' => env('ADMIN_DB_URL'),
            'host' => env('ADMIN_DB_HOST', '127.0.0.1'),
            'port' => env('ADMIN_DB_PORT', '5432'),
            'database' => env('ADMIN_DB_DATABASE', 'scf'),
            'username' => env('ADMIN_DB_USERNAME', 'root'),
            'password' => env('ADMIN_DB_PASSWORD', ''),
            'charset' => env('ADMIN_DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => env('ADMIN_DB_SCHEMA', 'admin'),
            'sslmode' => 'prefer',
        ],
        'test_laravel' => [
            'driver' => env('TEST_LARAVEL_DB_DRIVER', 'pgsql'),
            'url' => env('TEST_LARAVEL_DB_URL'),
            'host' => env('TEST_LARAVEL_DB_HOST', '127.0.0.1'),
            'port' => env('TEST_LARAVEL_DB_PORT', '5432'),
            'database' => env('TEST_LARAVEL_DB_DATABASE', 'scf_test'),
            'username' => env('TEST_LARAVEL_DB_USERNAME', 'root'),
            'password' => env('TEST_LARAVEL_DB_PASSWORD', ''),
            'charset' => env('TEST_LARAVEL_DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => env('TEST_LARAVEL_DB_SCHEMA', 'public'),
            'sslmode' => 'prefer',
        ],
        'test_my' => [
            'driver' => env('TEST_MY_DB_DRIVER', 'pgsql'),
            'url' => env('TEST_MY_DB_URL'),
            'host' => env('TEST_MY_DB_HOST', '127.0.0.1'),
            'port' => env('TEST_MY_DB_PORT', '5432'),
            'database' => env('TEST_MY_DB_DATABASE', 'scf_test'),
            'username' => env('TEST_MY_DB_USERNAME', 'root'),
            'password' => env('TEST_MY_DB_PASSWORD', ''),
            'charset' => env('TEST_MY_DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => env('TEST_MY_DB_SCHEMA', 'my'),
            'sslmode' => 'prefer',
        ],
        'test_manage' => [
            'driver' => env('TEST_MANAGE_DB_DRIVER', 'pgsql'),
            'url' => env('TEST_MANAGE_DB_URL'),
            'host' => env('TEST_MANAGE_DB_HOST', '127.0.0.1'),
            'port' => env('TEST_MANAGE_DB_PORT', '5432'),
            'database' => env('TEST_MANAGE_DB_DATABASE', 'scf_test'),
            'username' => env('TEST_MANAGE_DB_USERNAME', 'root'),
            'password' => env('TEST_MANAGE_DB_PASSWORD', ''),
            'charset' => env('TEST_MANAGE_DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => env('TEST_MANAGE_DB_SCHEMA', 'manage'),
            'sslmode' => 'prefer',
        ],
        'test_admin' => [
            'driver' => env('TEST_ADMIN_DB_DRIVER', 'pgsql'),
            'url' => env('TEST_ADMIN_DB_URL'),
            'host' => env('TEST_ADMIN_DB_HOST', '127.0.0.1'),
            'port' => env('TEST_ADMIN_DB_PORT', '5432'),
            'database' => env('TEST_ADMIN_DB_DATABASE', 'scf_test'),
            'username' => env('TEST_ADMIN_DB_USERNAME', 'root'),
            'password' => env('TEST_ADMIN_DB_PASSWORD', ''),
            'charset' => env('TEST_ADMIN_DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => env('TEST_ADMIN_DB_SCHEMA', 'admin'),
            'sslmode' => 'prefer',
        ],

        'admin_mongo' => [
            'driver' => 'mongodb',
            'dsn' => env('ADMIN_MONGO_DSN'),
            'database' => env('ADMIN_MONGO_DATABASE', 'scf_admin'),
        ],
        'my_mongo' => [
            'driver' => 'mongodb',
            'dsn' => env('MY_MONGO_DSN'),
            'database' => env('MY_MONGO_DATABASE', 'scf_my'),
        ],
        'manage_mongo' => [
            'driver' => 'mongodb',
            'dsn' => env('MANAGER_MONGO_DSN'),
            'database' => env('MANAGER_MONGO_DATABASE', 'scf_manage'),
        ],
        'api_manage_mongo' => [
            'driver' => 'mongodb',
            'dsn' => env('MANAGER_MONGO_DSN'),
            'database' => env('MANAGER_MONGO_DATABASE', 'scf_manage'),
        ],
        'api_admin_mongo' => [
            'driver' => 'mongodb',
            'dsn' => env('ADMIN_MONGO_DSN'),
            'database' => env('ADMIN_MONGO_DATABASE', 'scf_admin'),
        ],
        'api_my_mongo' => [
            'driver' => 'mongodb',
            'dsn' => env('MY_MONGO_DSN'),
            'database' => env('MY_MONGO_DATABASE', 'scf_my'),
        ],
        'mongo' => [
            'driver' => 'mongodb',
            'dsn' => env('MONGO_DSN'),
            'database' => env('MONGO_DATABASE', 'scf'),
        ],
        'test_mongo' => [
            'driver' => 'mongodb',
            'dsn' => env('TEST_MONGO_DSN'),
            'database' => env('TEST_MONGO_DATABASE', 'scf_test'),
        ],
        'test_admin_mongo' => [
            'driver' => 'mongodb',
            'dsn' => env('TEST_ADMIN_MONGO_DSN'),
            'database' => env('TEST_ADMIN_MONGO_DATABASE', 'admin_test'),
        ],
        'test_my_mongo' => [
            'driver' => 'mongodb',
            'dsn' => env('TEST_MY_MONGO_DSN'),
            'database' => env('TEST_MY_MONGO_DATABASE', 'my_test'),
        ],
        'test_manage_mongo' => [
            'driver' => 'mongodb',
            'dsn' => env('TEST_MANAGER_MONGO_DSN'),
            'database' => env('TEST_MANAGER_MONGO_DATABASE', 'manage_test'),
        ],
        // @TODO سناریو ایجاد و ویرایش و حذف مشکل داشت.
//        'api_my' => [
//            'query' => \Modules\Basic\Models\APIQuery::class,
//            'driver' => 'api_driver',
//            'remote_url' => env('MY_BASE_MODEL_API'),
//            'remote_connection' => 'my'
//        ],
        'api_my' => [
            'driver' => env('MY_DB_DRIVER', 'pgsql'),
            'url' => env('MY_DB_URL'),
            'host' => env('MY_DB_HOST', '127.0.0.1'),
            'port' => env('MY_DB_PORT', '5432'),
            'database' => env('MY_DB_DATABASE', 'scf'),
            'username' => env('MY_DB_USERNAME', 'root'),
            'password' => env('MY_DB_PASSWORD', ''),
            'charset' => env('MY_DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => env('MY_DB_SCHEMA', 'my'),
            'sslmode' => 'prefer',
        ],


        // @TODO سناریو ایجاد و ویرایش و حذف مشکل داشت.
//        'api_manage' => [
//            'query' => \Modules\Basic\Models\APIQuery::class,
//            'driver' => 'api_driver',
//            'remote_url' => env('MANAGE_BASE_MODEL_API'),
//            'remote_connection' => 'manage'
//        ],
        'api_manage' => [
            'driver' => env('MANAGE_DB_DRIVER', 'pgsql'),
            'url' => env('MANAGE_DB_URL'),
            'host' => env('MANAGE_DB_HOST', '127.0.0.1'),
            'port' => env('MANAGE_DB_PORT', '5432'),
            'database' => env('MANAGE_DB_DATABASE', 'scf'),
            'username' => env('MANAGE_DB_USERNAME', 'root'),
            'password' => env('MANAGE_DB_PASSWORD', ''),
            'charset' => env('MANAGE_DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => env('MANAGE_DB_SCHEMA', 'manage'),
            'sslmode' => 'prefer',
        ],
        'api_admin' => [
            'driver' => env('ADMIN_DB_DRIVER', 'pgsql'),
            'url' => env('ADMIN_DB_URL'),
            'host' => env('ADMIN_DB_HOST', '127.0.0.1'),
            'port' => env('ADMIN_DB_PORT', '5432'),
            'database' => env('ADMIN_DB_DATABASE', 'scf'),
            'username' => env('ADMIN_DB_USERNAME', 'root'),
            'password' => env('ADMIN_DB_PASSWORD', ''),
            'charset' => env('ADMIN_DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => env('ADMIN_DB_SCHEMA', 'admin'),
            'sslmode' => 'prefer',
        ],
        'test_api_my' => [
//            'query' => \Modules\Basic\Models\APIQuery::class,
//            'driver' => 'api_driver',
//            'remote_url' => env('TEST_MY_BASE_MODEL_API'),
//            'remote_connection' => 'test_my',
            'driver' => env('TEST_MY_DB_DRIVER', 'pgsql'),
            'url' => env('TEST_MY_DB_URL'),
            'host' => env('TEST_MY_DB_HOST', '127.0.0.1'),
            'port' => env('TEST_MY_DB_PORT', '5432'),
            'database' => env('TEST_MY_DB_DATABASE', 'scf'),
            'username' => env('TEST_MY_DB_USERNAME', 'root'),
            'password' => env('TEST_MY_DB_PASSWORD', ''),
            'charset' => env('TEST_MY_DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => env('TEST_MY_DB_SCHEMA', 'my'),
            'sslmode' => 'prefer',
        ],
        'test_api_manage' => [
//            'query' => \Modules\Basic\Models\APIQuery::class,
//            'driver' => 'api_driver',
//            'remote_url' => env('TEST_MANAGE_BASE_MODEL_API'),
//            'remote_connection' => 'test_manage'
            'driver' => env('TEST_MANAGE_DB_DRIVER', 'pgsql'),
            'url' => env('TEST_MANAGE_DB_URL'),
            'host' => env('TEST_MANAGE_DB_HOST', '127.0.0.1'),
            'port' => env('TEST_MANAGE_DB_PORT', '5432'),
            'database' => env('TEST_MANAGE_DB_DATABASE', 'scf'),
            'username' => env('TEST_MANAGE_DB_USERNAME', 'root'),
            'password' => env('TEST_MANAGE_DB_PASSWORD', ''),
            'charset' => env('TEST_MANAGE_DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => env('TEST_MANAGE_DB_SCHEMA', 'manage'),
            'sslmode' => 'prefer',

        ],
        'test_api_admin' => [
            'driver' => env('TEST_ADMIN_DB_DRIVER', 'pgsql'),
            'url' => env('TEST_ADMIN_DB_URL'),
            'host' => env('TEST_ADMIN_DB_HOST', '127.0.0.1'),
            'port' => env('TEST_ADMIN_DB_PORT', '5432'),
            'database' => env('TEST_ADMIN_DB_DATABASE', 'scf'),
            'username' => env('TEST_ADMIN_DB_USERNAME', 'root'),
            'password' => env('TEST_ADMIN_DB_PASSWORD', ''),
            'charset' => env('TEST_ADMIN_DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => env('TEST_ADMIN_DB_SCHEMA', 'admin'),
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
    | the migrations on disk haven't actually been run on the database.
    |
    */

    'migrations' => [
        'table' => 'migrations',
        'update_dw tate_on_publish' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as Memcached. You may define your connection settings here.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'scf'), '_') . '_database_'),
            'persistent' => env('REDIS_PERSISTENT', false),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'username' => env('REDIS_USERNAME'),
            'password' => env('REDIS_PASSWORD'),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],
        'test' => [
            'url' => env('TEST_REDIS_URL'),
            'host' => env('TEST_REDIS_HOST', '127.0.0.1'),
            'username' => env('TEST_REDIS_USERNAME'),
            'password' => env('TEST_REDIS_PASSWORD'),
            'port' => env('TEST_REDIS_PORT', '6379'),
            'database' => env('TEST_REDIS_DB', '2'),
        ],

    ],

];
