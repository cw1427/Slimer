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
        
        $container['appErrorHandler'] = function ($c) {
            return new \Slimer\ErrorHandler($c);
        };
        $container['notFoundHandler'] = function ($c) {
            return function (ServerRequestInterface $request, ResponseInterface $response) use ($c) {
                return $c['appErrorHandler']->error404($request, $response);
            };
        };

        $container['controller'] = $this->setControllerLoader($container);
        $container['errorHandler'] = $this->setErrorHandler($container);
        $container['phpErrorHandler'] = $this->setErrorHandler($container);
        $container['commandRunner'] = function() use ($container) {
	    if (PHP_SAPI == 'cli') {
                global $argv;
                $argv[1] = strtolower($argv[1]);
            }
            return new \adrianfalleiro\SlimCLIRunner($container);
        };
        
        $container['smtpMailer'] = function () use ($container) {
            return new \Nette\Mail\SmtpMailer($container['config']('mail'));
        };
        $container['smtpMessage'] = $container->factory(function () use ($container) {
            return new \Nette\Mail\Message();
        });
        $container['shellCommand'] = $container->protect(function ($command) use ($container) {
            return new \mikehaertl\shellcommand\Command($command);
        });
        $container['httpClient'] = $container->protect(function ($configArray=[]) use ($container) {
            $ca = \array_merge($configArray,['timeout'=>60,'verify'=>false]);
            return new \GuzzleHttp\Client($ca);
        });
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
        return $container->protect(function ($name,$clz=null) use ($container) {
            $parts = \explode('_', $name);
            $class = (isset($clz) && $clz !=null) ? $clz : $container['config']('suit.namespaces.controller', '\\App\\Controller\\');
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
