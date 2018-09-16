<?php
/**
 * Author: Shawn Chen
 * Desc: The HTML template engine config
 */
return [
    'template_path' => APP_PATH . DS . 'Templates',
    'cache_path' =>  APP_PATH . DS . 'Cache' ,
    'auto_reload' => ('prod' === \getenv('APP_ENV')) ? false : true
];
