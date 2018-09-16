<?php
/**
 * Author: Shawn Chen
 * Desc: The Slimer basic provider to extend the Slimer container by adding the config, and app_router feature
 */

namespace Slimer;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slimer\Router;
use Slimer\Config;
use Exception;

/**
 * Slimer Service Provider.
 */
class Provider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $container)
    {
        $container['suit_config'] = function ($c) {
            return new Config($c);
        };
        $container['config'] = $container->protect(function ($string, $default = null) use ($container) {
            return $container['suit_config']->__invoke($string, $default);
        });
        $container['app_router'] = function ($c) {
            return new Router($c);
        };
        $container['globalrequest_middleware'] = $container->protect(function ($request, $response, $next) use ($container) {
            if ($container->has('request')) {
                unset($container['request']);
                $container['request'] = $request;
            }
            
            return $next($request, $response);
        });

        $container['controller'] = $this->setControllerLoader($container);
        $container['errorHandler'] = $this->setErrorHandler($container);
        $container['phpErrorHandler'] = $this->setErrorHandler($container);
    }
    
    /**
     * Set controller() function into container.
     *
     * @param Container $container
     *
     * @return callable
     */
    protected function setControllerLoader(Container $container)
    {
        return $container->protect(function ($name) use ($container) {
            $parts = \explode('_', $name);
            $class = $container['config']('suit.namespaces.controller', '\\App\\Controller\\');
            foreach ($parts as $part) {
                $class .= \ucfirst($part);
            }
            if (!$container->has('controller_'.$class)) {
                $container['controller_'.$class] = function ($container) use ($class) {
                    return new $class($container);
                };
            }
            
            return $container['controller_'.$class];
        });
    }
    
    /**
     * Set error handler with sentry.
     *
     * @param Container $container
     *
     * @return callable
     */
    protected function setErrorHandler(Container $container)
    {
        return function (Container $container) {
            return function (ServerRequestInterface $request, ResponseInterface $response, Exception $e) use ($container) {
                if ($container->has('appErrorHandler')) {
                    return $container['appErrorHandler']->error500($request, $response, $e);
                }
                
                return $response->withStatus(500);
            };
        };
    }
}
