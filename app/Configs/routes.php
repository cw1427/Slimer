<?php

/**
 * Author: Shawn Chen
 * Desc: The route set entrance file
 */

$routes = [
    '/' => [ //Default group for /
        'main' => [ //route name and action in controller
            'pattern' => '', //pattern
            'methods' => ['GET'], //Allowed HTTP methods
        ],
        'index' => [ //route name and action in controller
            'pattern' => 'index', //pattern
            'methods' => ['GET'], //Allowed HTTP methods
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
	    'user_profile_passwordchange' => [
            'pattern' => 'changepassword',
            'methods' => ['POST']
        ],
        'logout' => [
            'pattern' => 'logout',
            'methods' => ['GET']
        ],
        'introed' => [
            'pattern' => 'introed',
            'methods' => ['GET']
        ],
        'unintroed'=>[
            'pattern' => 'unintroed',
            'methods' => ['GET']
        ],
        'sbs_adminlte_show_task' => [
            'pattern' => 'sbs_adminlte_show_task[/{taskId}]',
            'methods' => ['GET']
        ],
        'sbs_adminlte_all_tasks' => [
            'pattern' => 'sbs_adminlte_all_tasks',
            'methods' => ['GET']
        ]
    ],
];

foreach (\glob(__DIR__.'/routes/*.php') as $item) {
    $group = \current(\explode('.', \basename($item)));
    $routes['/'.$group] = include $item;
}

return $routes;
