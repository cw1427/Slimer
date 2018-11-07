<?php
/**
 * Author: Shawn Chen
 * Desc: The authentication global middleware
 */
namespace Slimer\Auth\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slimer\Root;

/**
 * Role-Based Access Control.
 */
class Auth extends Root
{
    /**
     * Run RBAC check.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param callable               $next
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        //----by pass some unnecessary request
        $route = $request->getAttribute('route');
        //404 if route not found
        if (!$route) {
            if ($this->container->has("basicAuth")){
                return $this->response->withStatus(404)->withJson(['message'=>'The request url path is not found ' . $request->getUri()->getPath()]);
            }else{
                return $this->notFoundHandler->__invoke($request, $response);
            }
        }        
        if ($route->getName() == 'login') return $next($request, $response);
        //----check if has a valid session
        if ($this->container->has("basicAuth") || ($this->container->has('user') && $this->container->get('user'))){
            return $next($request, $response);
        }else{
            $this->container['flash']->addMessage('danger','Require login');
            return $this->response->withRedirect('/login');
        }
        
    }
    
    
}