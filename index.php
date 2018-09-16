<?php
/*
 * Author: Shawn Chen
 * Desc: The entrance of Slimer, load autoload file and call Slimer App.
 */

if (file_exists('vendor/autoload.php')) {
    require 'vendor/autoload.php';
} else {
    echo "<h1>Please install via composer.json</h1>";
    echo "<p>Install Composer instructions: <a href='https://getcomposer.org/doc/00-intro.md#globally'>https://getcomposer.org/doc/00-intro.md#globally</a></p>";
    echo "<p>Once composer is installed navigate to the working directory in your terminal/command promt and enter 'composer install'</p>";
    exit;
}

// if (\extension_loaded('session')) {
//     \session_start();
// }

define('DS', DIRECTORY_SEPARATOR);
define('APP_ROOT',__DIR__);
define('APP_PATH',APP_ROOT . DS . 'app');
define('ENVIRONMENT', 'development');
define('LOG_PATH', APP_ROOT . DS . 'logs');
// call Slim
require './app/Main.php';
