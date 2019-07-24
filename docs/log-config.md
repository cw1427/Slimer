# Slimer logger config

Slimer use [monolog](https://packagist.org/packages/monolog/monolog) to implement its log rotate.
```PHP
//----log.php
<?php
/**
 * Author: Shawn Chen
 * Desc: The monolog log config
 */
return [
    'channel' => 'slimer',
    'level' => ('prod' === \getenv('APP_ENV')) ? \Monolog\Logger::WARNING : \Monolog\Logger::DEBUG,
];


//----logger in provider 
        $container['logger'] = function ($c) {
            $config = $c['config']('log');
            $logger = new \Monolog\Logger(isset($config['channel'] ) ? $config['channel'] : 'app');
            $streamHandler = new \Monolog\Handler\StreamHandler(LOG_PATH . DS .'gam_'.date("Ymd").'.log');
            $introspection = new \Monolog\Processor\IntrospectionProcessor (
                \Monolog\Logger::DEBUG, // whatever level you want this processor to handle
                [
                    'Monolog\\',
                    'Illuminate\\',
                ]
            );
            $logger->pushHandler($streamHandler);
            $logger->pushProcessor($introspection);
            return $logger;
        };
```

> logger usage

Slimmer logger will be store under logs folder as the config in the log.php, logger will have below different log level.

- info
- debug
- error
- warning

```PHP
$this->logger->info(...)
```