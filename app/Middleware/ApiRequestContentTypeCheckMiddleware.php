<?php
/**
 * Author: Shawn Chen
 * Desc: ApiRequestContentTypeCheckMiddleware check if Rest api request has json content type
 */
namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slimer\Root;

/**
 * Restriction login middleware.
 */
class ApiRequestContentTypeCheckMiddleware extends Root
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
        $ct = $request->getContentType();
        if ($ct !== "application/json"){
            return $response->withStatus(400)->withJson(['error'=>['message'=>'wrong request content type,','code'=>400],
                'tip'=>'please add Content-Type=application/json in the header']);
        }
        return $next($request, $response);
    }


}