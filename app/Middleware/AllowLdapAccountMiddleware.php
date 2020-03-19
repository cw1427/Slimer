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
class AllowLdapAccountMiddleware extends Root
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
        if ($this->user->get('type') != 'ldap'){
            $this->flash->addMessage('warning','Local user not allow to request a gerrit group');
            return $this->response->withRedirect('/admin/admin');
        }
        return $next($request, $response);
    }


}