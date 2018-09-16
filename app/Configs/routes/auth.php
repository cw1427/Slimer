<?php

return [
    'login' => [
        'pattern' => '/login',
        'methods' => ['GET','POST'],
        'rbac' => [
            'anonymous' => ['GET','POST'],
        ],
    ],
    'logout' => [
        'pattern' => '/logout',
        'rbac' => [
            'user' => ['GET'],
        ],
    ],

];