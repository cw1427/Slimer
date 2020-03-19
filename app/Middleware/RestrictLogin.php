<?php
/**
 * Author: Shawn Chen
 * Desc: The temporary restriction for user login global middleware
 */
namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slimer\Root;

/**
 * Restriction login middleware.
 */
class RestrictLogin extends Root
{
    /**
     * Run restrict login middleware.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param callable               $next
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $route = $request->getAttribute('route');
        if ($route->getName() == 'login' || $route->getName() == 'logout') {
            return $next($request, $response);
        }
        //----get current login user
        if (in_array($this->user->get('loginName'), $this->config('suit.allow_login_user'))){
            return $next($request, $response);
        }else{
            $this->container['flash']->addMessage('danger','Temporary restrict login');
            return $this->response->withRedirect($this->router->pathFor('logout'));
        }
 
    }
    
    
}