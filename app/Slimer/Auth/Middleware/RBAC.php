<?php
/**
 * Author: Shawn Chen
 * Desc: TODO  A basic RBAC permission controll middleware to do the permission controll
 */
namespace Slimer\Auth\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slimer\Root;

/**
 * Role-Based Access Control.
 */
class RBAC extends Root
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
        
        $route = $request->getAttribute('route');
        $groupName = \trim($route->getGroups()[0]->getPattern(),'/');
        $groupName = \str_replace('/','-',$groupName);
        $routeName = $route->getName();
        $rns = \explode('-',$routeName);
        $routeName=\end($rns);
        $routeConf = $this->config('routes')['/'.$groupName][$routeName];
        if (isset($routeConf['perm'])){
            $request=$request->withAttribute('perm',$routeConf['perm']);
            $permGroup = $this->user->get('perm_group');
            if (isset($permGroup) && $permGroup != null){
                $permGroupIds = [];
                foreach ($permGroup as $group){
                    \array_push($permGroupIds,$group['ID']);
                }
                foreach ($routeConf['perm'] as $perm){
                    if ($this->rbac->check($perm,$permGroupIds)){
                        return $next($request, $response);
                    }
                }
            }
            return $this->appErrorHandler->error403($request,$response);
        }else{
            return $next($request, $response);
        }
    }
}
