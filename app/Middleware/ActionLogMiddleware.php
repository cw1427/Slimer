<?php
/**
 * Author: Shawn Chen
 * Desc: ActionLog middleware
 */
namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slimer\Root;

/**
 * Restriction login middleware.
 */
class ActionLogMiddleware extends Root
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
        $rn = \explode('-',$route->getName());
        if (\count($rn)==1){
            $rn=['',$rn[0]];
        }
        if ( \array_key_exists($rn[0], $this->container['config']('suit.api_route_mapping')) ){
            $routeConf = $this->container['config']('routes')[$this->container['config']('suit.api_route_mapping')[$rn[0]]][$rn[1]];
        }else{
            $routeConf = $this->container['config']('routes')["/{$rn[0]}"][$rn[1]];
        }
        $params = $request->getParams();
        $actionLogModel = $this->entity('ActionLog');
        $actionLogModel->setData(['route'=>$route->getName(),'uri'=>$route->getPattern(),'desc'=>isset($routeConf['desc'])?$routeConf['desc']:$rn[1],
            'operator'=>"{$this->user->get('userName')}({$this->user->get('loginName')})",'actiontime'=>time(),'params'=>\json_encode($params)
        ]);
        $actionLogModel->save();
        return $next($request, $response);
    }


}