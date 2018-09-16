<?php

/**
 * Author: Shawn Chen
 * Desc: The route set entrance file
 */

$routes = [
    '/' => [ //Default group for /
        'index' => [ //route name and action in controller
            'pattern' => '', //pattern
            'methods' => ['GET'], //Allowed HTTP methods
            'rbac' => [ //Role-Based Access Controll
                'anonymous' => ['GET'], //Key is role name, value is array of allowed HTTP methods for that role
            ],
        ],
        'second' => [ //route name
            'pattern' => 'second', //pattern
            'rbac' => [
                'anonymous' => ['GET'],
            ],
            //all other fields will be defaults
            //methods = ['GET']
        ],
        'login' => [
            'pattern' => 'login',
            'methods' => ['GET','POST'],
        ],
        'sbs_adminlte_sidebar_collapse' => [
            'pattern' => 'sidebarcollapse',
            'methods' => ['POST']
        ],
        'sbs_adminlte_user_profile' => [
            'pattern' => 'userprofile',
            'methods' => ['GET']
        ],
        'logout' => [
            'pattern' => 'logout',
            'methods' => ['GET']
        ],
        'profile' => [
            'pattern' => 'profile',
            'methods' => ['GET']
        ]
    ],
];

foreach (\glob(__DIR__.'/routes/*.php') as $item) {
    $group = \current(\explode('.', \basename($item)));
    $routes['/'.$group] = include $item;
}

return $routes;
