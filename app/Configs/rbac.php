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