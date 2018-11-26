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
        'label' => 'Admin',
        'route' => '',
        'icon' => 'fa fa-cog',
        'children' => [
            [
                'id' => uniqid(),
                'active' => false,
                'label' => 'Approval Delegation',
                'route' => '/admin/admapprovaldeligate',
                'routeName' => '/admin/approval_deligate',
                'icon' => 'fa fa-arrows-h'
            ],
            [
                'id' => uniqid(),
                'active' => false,
                'label' => 'User',
                'route' => '/admin/usermanage',
                'routeName' => '/admin/user_manage',
                'icon' => 'fa fa-user'
            ],
            [
                'id' => uniqid(),
                'active' => false,
                'label' => 'Group',
                'route' => '/admin/groupmanage',
                'routeName' => '/admin/group_manage',
                'icon' => 'fa fa-group'
            ],
            [
                'id' => uniqid(),
                'active' => false,
                'label' => 'Role',
                'route' => '/admin/rolemanage',
                'routeName' => '/admin/role_manage',
                'icon' => 'fa fa-paw'
            ],
            [
                'id' => uniqid(),
                'active' => false,
                'label' => 'Permission',
                'route' => '/admin/permmanage',
                'routeName' => '/admin/perm_manage',
                'icon' => 'fa fa-gear'
            ],
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
