<?php
/**
 * Author: Shawn Chen
 * Desc: Slimer view layer extension provider
 */
namespace Slimer\Html;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class Provider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['csrf_middleware'] = function (Container $container) {
            $guard = new \Slim\Csrf\Guard();
            if ($callable = $container['config']('csrf.failure_callable')) {
                $guard->setFailureCallable($callable);
            }
            
            return $guard;
        };
        
        $container['baseurl_middleware'] = function ($c) {
            return new \Slimer\Html\Middleware\Baseurl($c);
        };
        
        $container['flash'] = function (Container $container) {
            return new \Slim\Flash\Messages();
        };
        
        $container['view'] = function (Container $container) {
            $settings = $container['config']('html', []);
            $view = new \Slim\Views\Twig($settings['template_path'], [
                'cache' => $settings['cache_path'],
                'auto_reload' => $settings['cache_path']
            ]);
            
            // Instantiate and add Slim specific extension
            $basePath = \rtrim(\str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
            $view->addExtension(new \Slim\Views\TwigExtension($container['router'], $basePath));
            $view->addExtension(new \Knlv\Slim\Views\TwigMessages($container['flash']));
            $view->addExtension(new \Slimer\Html\CsrfExtension($container['csrf_middleware']));
            $view->addExtension(new \nochso\HtmlCompressTwig\Extension());
            
            $view->addExtension(new \Slimer\Html\SideBarExtension($container));
            $view->addExtension(new \Slimer\Html\NavBarExtension($container));
            return $view;
        };
    }
}