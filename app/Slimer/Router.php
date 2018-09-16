<?php
/**
 * Author: Shawn Chen
 * Desc: The auto route set generator doing a mapping for the route config item and its related Controller.
 */
namespace Slimer;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Application level router.
 */
class Router extends \Slimer\Root
{
    /**
     * Map routes from `routes.php` config.
     *
     * @param \Slim\App $app
     */
    public function __invoke(\Slim\App $app)
    {
        foreach ($this->config('routes') as $group_name => $routes) {
            $app->group($group_name, function () use ($group_name, $routes){
                $controller = ('/' === $group_name || !$group_name) ? 'index' : \trim($group_name, '/');
                
                foreach ($routes as $name => $route) {
                    $methods = isset($route['methods']) ? $route['methods'] : ['GET'];
                    $pattern = isset($route['pattern']) ? $route['pattern'] : '';
                    $callable = function (Request $request, Response $response, array $args = []) use ($controller, $route) {
                        return $this['controller']($controller)->__invoke($request, $response, $args);
                    };
                    $this->map($methods, $pattern, $callable)->setName(('index' === $controller ? '' : $controller . '-').$name);
                }
            });
        }
    }
}