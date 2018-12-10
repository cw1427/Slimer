<?php

/**
 * Author: Shawn Chen
 * Desc: The DB config
 */

return [
    'default' =>[
            'namespace' => '\App\Models\\',
            'database_type' => 'mysql',
            'database_name' => 'slimer',
            'server' => '127.0.0.1',
            'username' => 'root',
            'password' => 'www123#',
            'charset' => 'utf8',
            'port' => 3306,
            'option' => [
                PDO::ATTR_CASE => PDO::CASE_NATURAL,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ],
        ],
    
];