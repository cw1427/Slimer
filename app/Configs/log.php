<?php
/**
 * Author: Shawn Chen
 * Desc: The monolog log config
 */
return [
    'channel' => 'gam',
    'level' => ('prod' === \getenv('APP_ENV')) ? \Monolog\Logger::WARNING : \Monolog\Logger::DEBUG,
];