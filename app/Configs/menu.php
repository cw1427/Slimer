<?php
/**
 * Author: Shawn Chen
 * Desc: The web page menu config
 */
return [
    [
        'id' => uniqid(),
        'active' => true,
        'label' => 'Portal',
        'route' => '/admin/admin',
        'icon' => 'fa fa-dashboard',
        'badges' => [
            'new' => 'green'
        ]
    ],
    [
        'id' => uniqid(),
        'active' => false,
        'label' => 'charts',
        'route' => 'auth/second',
        'icon' => 'fa fa-th',
        'children' => [
            [
                'id' => uniqid(),
                'active' => true,
                'label' => 'ChartJS',
                'route' => '/auth/login',
		'routeName' => '/auth/loginname',
                'icon' => 'fa fa-th'
            ],
            [
                'id' => uniqid(),
                'active' => true,
                'label' => 'Flot',
                'route' => '/auth/login',
		'routeName' => '/auth/loginname',
                'icon' => 'fa fa-th'
            ]
        ]
    ],
];
