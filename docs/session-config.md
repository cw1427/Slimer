# Slimer session config

Slimer use bryanjhv/slim-session library to implement session setup. [Slim session](https://github.com/bryanjhv/slim-session)

- lifetime: How much should the session last? Default 20 minutes. Any argument that strtotime can parse is valid.
- path, domain, secure, httponly: Options for the session cookie.
- name: Name for the session cookie. Defaults to slim_session (instead of PHP's PHPSESSID).
- autorefresh: true if you want session to be refresh when user activity is made (interaction with server).
- handler: Custom session handler class or object. Must implement SessionHandlerInterface as required by PHP.
- ini_settings: Associative array of custom session configuration. Previous versions of this package had some hardcoded values which could bring     serious performance leaks (see #30):

```PHP
<?php
/**
 * Author: Shawn Chen
 * Desc: 
 */

return  [
    'name' => 'PHPSESSION',
    'lifetime' => '24 hour',
    'autorefresh' => false,
    'path' => '/',
    'domain' => null,
    'secure' => false,
    //'httponly' => true,
    //'handler' => null,
    'ini_settings' => [
        'session.save_path' => APP_ROOT . DS . 'session',
    ]
];

```
> Register session middleware in the container in the Slimer/Auth/Provider

```PHP
        $container['session'] = function (Container $container) {
            return new \SlimSession\Helper;
        };
        $container['session_middleware'] = function (Container $container) {
            return new \Slim\Middleware\Session($container['config']('session'));
        };
```

> Usage about session

```PHP
$user = $this->session->get('user');
$this->session->set('key',$value);

```


