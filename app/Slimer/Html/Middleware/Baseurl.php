<?php
/**
 * Author: Shawn Chen
 * Desc: Middleware to extract the basic url path for every request
 */

namespace Slimer\Html\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Add `baseurl` key into container with current base url.
 *
 * @see https://www.slimframework.com/docs/concepts/middleware.html
 *
 * @example https://example.com
 * @example http://example.com
 * @example https://example.com:8443
 */
class Baseurl extends \Slimer\Root
{
    /**
     * @see https://www.slimframework.com/docs/concepts/middleware.html
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param callable               $next
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $url = $request->getUri()->getScheme().'://'.$request->getUri()->getHost();
        $url .= ('80' !== $request->getUri()->getPort() || '443' !== $request->getUri()->getPort()) ? ':'.$request->getUri()->getPort() : '';
        $this->container['baseurl'] = $url;
        return $next($request, $response);
    }
}
