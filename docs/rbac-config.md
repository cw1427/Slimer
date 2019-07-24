# RBAC init config

Slimer RBAC controller is based on [PHP RBAC](https://github.com/OWASP/rbac).

## RBAC default config

Slimer provide some basic role permission info in the rbac.php file under [Config dir](config?id=config-dir).

> RBAC config support for basic role, permission and role-permission relationship defination.

- **rbac_table_prefix**: the default rbac related table prefix.
- **rbac_role_path**: define the role name and its path , description. The key is the path.
- **rbac_perm_path**: define the permission name and its path, description. The key is the path.
- **rbac_role_perm**: define the role, permission relationship, the key is the role path, the value are the permission that belong to it

> the path about role and permission is unique and which "/<name>" means they have including relationship.

For example:"/jenkins_admin/jenkins_request" would indicate two role, one is /jenkins_admin, the other is jenkins_request, and the jenkins_admin includs jenkins_request.

Any group which had been granted to a role, it will have all of this role's descendant.

```PHP
<?php
/*
 * Author: Shawn Chen
 */

return [
    'rbac_table_prefix' => 'perm_',
    'rbac_role_path' => [
        '/jenkins_admin/jenkins_request'=>['jenkins module admin','jenkins requestor'],
        '/art_admin/art_request'=>['artifactory module adimn','Artifactory requestor'],
        '/gerrit_admin/gerrit_request' => ['gerrit module admin','gerrit requestor']
    ],
    'rbac_perm_path' => [
        '/jenkins_manage/jenkins_req'=>['jenkins manage perm','jenkins request perm'],
        '/art_manage/art_req'=>['art manage perm','art request perm'],
        '/gerrit_manage/gerrit_req' => ['gerrit manage perm','gerrit request perm'],
        '/approval'=>['Approval delgate perm'],
        '/art_jenkins_list' => ['Allow show art jenkins group list']
    ],
    'rbac_role_perm' => [
        'jenkins_admin'=>['jenkins_manage','approval'],
        '/jenkins_admin/jenkins_request' => ['/jenkins_manage/jenkins_req'],
        'art_admin' => ['art_manage','approval'],
        '/art_admin/art_request' => ['/art_manage/art_req'],
        'gerrit_admin' => ['gerrit_manage','approval'],
        '/gerrit_admin/gerrit_request' => ['/gerrit_manage/gerrit_req']
    ]
];
```

## RBAC init command

Slimer provide some CLI command to init it. Please refer [RBAC](rbac.md)