<?php

$cache_dir = APP_PATH . DS . 'Cache';

return [
    'providers' => [
        '\Slimer\Html\Provider',
        '\Slimer\Auth\Provider',
        '\Slimer\Orm\Provider',
        '\App\Provider\Provider',
    ],

    'middlewares' => [
        'rbac_middleware',  //----checking the roles meet the route or not
        'auth_middleware',  //----fetch the use related roles in the request
	'httpauth_middleware', //----basic authen before original auth
        'baseurl_middleware',
        'session_middleware', //----checking the request session info
	'commandRunner',//----add cli command middleware
    ],
    
    'namespaces' => [
        'controller' => '\\App\\Controller\\',
    ],
    
    'commands' => [
        'cmd' => '\App\Command\Cmd',
        'sampletask' => '\App\Command\SampleTask',
        'dbinit' => '\App\Command\Dbinit',
        'rbacinit' => '\App\Command\RbacInit',
        'rbacreset' => '\App\Command\RbacReset',
        'rbacmanage' => '\App\Command\RbacManage',
    ],
    
    'settings' => [
        'displayErrorDetails' => ('prod' === \getenv('APP_ENV')) ? false : true,
        'determineRouteBeforeAppMiddleware' => true,
        'debug' => ('prod' === \getenv('APP_ENV')) ? false : true,
        'routerCacheFile' => ('prod' === \getenv('APP_ENV')) ? $cache_dir.'/routes.cache.php' : false,
        'renderer' => [
            'template_path' => APP_PATH . DS . 'Templates',
        ],
        // Monolog settings
        'logger' => new \Monolog\Logger('api',array(new \Monolog\Handler\StreamHandler(LOG_PATH . DS .'gam_'.date("Ymd").'.log'))),
    ],
    
    'httpauth' => [
        'path'=> ['/api'],  //----the basic path need to do the httpauth
        'passthrough' => null //----the white list bypass the httpauth
    ],
    
    'version_key' => 'VERSION',
    'commitid_key' => 'COMMITID'
];