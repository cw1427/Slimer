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
    
    'tasks'=> [],
    'notices'=>[],
    'actions' => [
        [
            'route'=>'unintroed',
            'icon'=>'fa fa-lightbulb-o',
            'message'=>'Show guidance',
            'data'=>[]
        ]
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
        ]
    ],
    
    'httpauth' => [
        'secure'=>false,
        'path'=> ['/api'],  //----the basic path need to do the httpauth
        'passthrough' => null, //----the white list bypass the httpauth
        "relaxed" => ["localhost", "127.0.0.1"],
    ],
    
    'version_key' => 'VERSION',
    'commitid_key' => 'COMMITID',
    'project_name' => 'Slimer', //----default is Slimer
    'intro_date' => '2018-12-31',  //-----if not setup will ignore the intro
    'introductions' => [
        'app'=>[
            'data-step' =>'1',
            'data-intro' => 'Slimer Guide, Please click skip or down to finish introduction.',
        ],
        'menuToggleButton' => [
            'data-step' =>'2',
            'data-intro' => 'Click here to toggle fold menu11111',
        ],
        'actions' => [
            'data-step' =>'3',
            'data-intro' => 'Several actions <br/> click show guidance to force introduction',
        ],
        'navBarUser' => [
            'data-step' =>'4',
            'data-intro' => 'Click here gos to Profile or Sign out',
            'data-position' => 'bottom-left-aligned'
        ],
        'quickSearch'=>[
            'data-step' =>'5',
            'data-intro' => 'Input key words about the menus lable for quick seach',
        ],
    ],
    'menu_visible_by_perm' => True
];