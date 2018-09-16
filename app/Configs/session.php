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
