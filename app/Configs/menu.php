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
        'route' => '/index',
        'routeName' => '/index',
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
            [
                'id' => uniqid(),
                'active' => false,
                'label' => 'Action Logs',
                'route' => '/admin/actionlog',
                'routeName' => '/admin/action_log',
                'icon' => 'fa fa-list-alt'
            ],
        ]
    ],
    [
        'id' => uniqid(),
        'active' => false,
        'label' => 'Samples',
        'route' => '',
        'icon' => 'fa fa-th',
        'children' => [
            [
                'id' => uniqid(),
                'active' => true,
                'label' => 'Forms',
                'route' => '/samples/form',
                'icon' => 'fa fa-th'
            ],
            [
                'id' => uniqid(),
                'active' => true,
                'label' => 'Tables',
                'route' => '/samples/table',
                'routeName' => '/samples/table',
                'icon' => 'fa fa-th'
            ],
        ]
    ],
];
