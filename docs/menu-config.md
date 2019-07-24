# Slimer Menu system

## Menu config

Slimer has a menu.php config file under [Config dir](config?id=config-dir).

> Menu array description

- **id**: the menu item unique id.
- **active**: the menu css style if set to true, it will be highlight as active in the menu ui.
- **label**: the menu content, it support for HTML character e.g.  < br/>
- **route**: this menu related route pattern, as default, it is the route' pattern.
- **routeName**: this menu related route name, it has a higher priority than route if the route's name not equal to pattern.
- **children**: Slimer support for recursive multi levels menu.
- **badges**: The menu css style, allow show the badges info in the menu item.
- **icon**: The menu icon.

```PHP
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

```