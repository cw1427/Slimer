<?php
/**
 * Author: Shawn Chen
 * Desc: 
 */

return [
    'admin' => [
        'pattern' => '/admin',
        'methods' => ['GET'],
    ],
    'approval_deligate' => [
        'pattern' => '/admapprovaldeligate',
        'methods' => ['GET'],
        'perm' => ['approval']
    ],
    'user_manage' => [
        'pattern' =>'/usermanage',
        'methods' => ['GET'],
    ],
    'get_users' => [
        'pattern' =>'/getusers',
        'methods' => ['GET','POST']
    ],
    'add_user' => [
        'pattern' =>'/adduser',
        'methods' => ['POST']
    ],
    'del_user' => [
        'pattern' =>'/deluser',
        'methods' => ['DELETE']
    ],
    'edit_user' =>  [
        'pattern' =>'/edituser/{uid}',
        'methods' => ['GET']
    ],
    'userinfo_edit' =>  [
        'pattern' =>'/edituserinfo',
        'methods' => ['POST']
    ],
    'password_change' =>  [
        'pattern' =>'/changepwd',
        'methods' => ['POST']
    ],
    'usergroup_edit' => [
        'pattern' =>'/editusergroup',
        'methods' => ['POST']
    ],
    //----group related route
    'group_manage' => [
        'pattern' =>'/groupmanage',
        'methods' => ['GET'],
    ],
    'get_groups' => [
        'pattern' =>'/getgroups',
        'methods' => ['GET','POST']
    ],
    'add_group' => [
        'pattern' =>'/addgroup',
        'methods' => ['POST']
    ],
    'del_group' => [
        'pattern' =>'/delgroup',
        'methods' => ['DELETE']
    ],
    'edit_group' =>  [
        'pattern' =>'/editgroup/{uid}',
        'methods' => ['GET']
    ],
    'groupinfo_edit' =>  [
        'pattern' =>'/editgroupinfo',
        'methods' => ['POST']
    ],
    'grouprole_edit' => [
        'pattern' =>'/editgrouprole',
        'methods' => ['POST']
    ],
    'groupuser_edit' => [
        'pattern' =>'/editgroupuser',
        'methods' => ['POST']
    ],
    //----role related route
    'role_manage' => [
        'pattern' =>'/rolemanage',
        'methods' => ['GET'],
    ],
    'get_roles' => [
        'pattern' =>'/getroles',
        'methods' => ['GET','POST']
    ],
    'add_role' => [
        'pattern' =>'/addrole',
        'methods' => ['POST']
    ],
    'del_role' => [
        'pattern' =>'/delrole',
        'methods' => ['DELETE']
    ],
    'edit_role' =>  [
        'pattern' =>'/editrole/{uid}',
        'methods' => ['GET']
    ],
    'roleinfo_edit' =>  [
        'pattern' =>'/editroleinfo',
        'methods' => ['POST']
    ],
    'roleperm_edit' => [
        'pattern' =>'/editroleperm',
        'methods' => ['POST']
    ],
    'rolegroup_edit' => [
        'pattern' =>'/editrolegroup',
        'methods' => ['POST']
    ],
    //-----permissions related route
    'perm_manage' => [
        'pattern' =>'/permmanage',
        'methods' => ['GET'],
    ],
    'get_perms' => [
        'pattern' =>'/getperms',
        'methods' => ['GET','POST']
    ],
    'add_perm' => [
        'pattern' =>'/addperm',
        'methods' => ['POST']
    ],
    'del_perm' => [
        'pattern' =>'/delperm',
        'methods' => ['DELETE']
    ],
    'edit_perm' =>  [
        'pattern' =>'/editperm/{uid}',
        'methods' => ['GET']
    ],
    'perminfo_edit' =>  [
        'pattern' =>'/editperminfo',
        'methods' => ['POST']
    ],
    'permrole_edit' => [
        'pattern' =>'/editpermrole',
        'methods' => ['POST']
    ]
];