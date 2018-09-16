<?php
/**
 * Author: Shawn Chen
 * Desc:
 */
return [
    'entity' => 'user', // user entity
    'storage' => \Slimer\Auth\Storage\Session::class, // can be Session, Cookie, JWT
    'repository' => \Slimer\Auth\Repository\LDAP::class, // default Db repository
    'rbac' => [
        'defaultRole' => 'anonymous', // default unauthorized role
        'errorCallback' => null
    ],
    'ldap' => [
        'server' => [
            'host' => '<your ldap server>',
            'port' => 389,
            'encryption' => 'none',
            'options' => [
                'protocol_version' => 3,
                'referrals' => true,
            ],
        ],
        'admin' => [
            'dn' => 'searchID=XX,ou=people,ou=intranet,dc=XXXX,dc=com',
            'password' =>'*****',
        ],
        'userDn' => 'searchID=%s,ou=people,ou=intranet,dc=XXXX,dc=com',
        'baseDN' => 'ou=people,ou=intranet,dc=XXXX,dc=com',
        'fields' => [
            'login' => ['id','uid', 'mail'],
            'loginInDb' => 'loginName',
            'map' => [
                'cn' => 'userName',
                'sn' =>'lastName',
                'givenName' => 'firstName',
                'mail' => 'email',
            ],
        ],
     ]
];