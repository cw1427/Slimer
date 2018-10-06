<?php 
/**
 * Author: Shawn Chen
 * Desc: App provider file to extend the Slimer Container.
 */
namespace App\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Application Service Provider.
 */
class Provider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $container)
    {
        $container['baseurl_middleware'] = function ($c) {
            return new \App\Middleware\Baseurl($c);
        };
        $container['appErrorHandler'] = function ($c) {
            return new \App\ErrorHandler($c);
        };
        $container['notFoundHandler'] = function ($c) {
            return function (ServerRequestInterface $request, ResponseInterface $response) use ($c) {
                return $c['appErrorHandler']->error404($request, $response);
            };
        };
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
        $container['service'] = $container->protect(function ($name) use ($container) {
            if (!$container->has('service_'.$name)) {
                $parts = \explode('_', $name);
                $class = 'App\\Service';
                foreach ($parts as $part) {
                    $class .= '\\'.\ucfirst($part);
                }
                $container['service_'.$name] = function ($container) use ($class) {
                    return new $class($container);
                };
            }
            
            return $container['service_'.$name];
        });
        
        $container['util'] = function () use ($container) {
            $instance =new \App\Util();
            $instance->setContainer($container);
            return $instance;
        };
        $container['smtpMailer'] = function () use ($container) {
            return new \Nette\Mail\SmtpMailer($container['config']('mail'));
        };
        $container['smtpMessage'] = function () use ($container) {
            return new \Nette\Mail\Message();
        };
        $container['shellCommand'] = $container->protect(function ($command) use ($container) {
            return new \mikehaertl\shellcommand\Command($command);
        });
        $container['httpClient'] = function () use ($container) {
            return new \GuzzleHttp\Client(['timeout'=>60]);
        };
          
          
    }
}