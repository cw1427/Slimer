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
        'baseurl_middleware',
        'session_middleware', //----checking the request session info
	'commandRunner',//----add cli command middleware
    ],
    
    'namespaces' => [
        'controller' => '\\App\\Controller\\',
    ],
    
    'commands' => [
        'SampleTask' => '\App\Command\SampleTask',
        'Dbinit' => '\App\Command\Dbinit',
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
];