<?php
/**
 * Author: Shawn Chen
 * Desc: The generic controller class for the route set mapping
 */

namespace Slimer;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slimer\Root;

class Controller extends Root
{
    /**
     * Response template.
     *
     * @var array
     */
    protected $defaultResponse = [
        'error' => [
            'message' => null,
            'fields' => [],
        ],
        'count' => 0,
        'data' => [],
    ];
    
    /**
     * Invoke controller.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param array                  $args
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->request = $request;
        $this->response = $response;
        if ($request->getAttribute('route')) {
            $name = \explode('-', $request->getAttribute('route')->getName());
            $action = \strtolower(\end($name)).'Action';
            
            if (\method_exists($this, $action)) {
                return \call_user_func([$this, $action],$args);
            }
            
            return $this->notFound();
        }
        
        return $this->notFound();
    }
    
    /**
     * Render view.
     *
     * @param string $template    Template file name
     * @param array  $vars        Template vars
     * @param int    $status_code HTTP status code, default: 200
     *
     * @return ResponseInterface
     */
    public function render( $template, array $vars = [],  $status_code = 200)
    {
        $vars['container'] = $this->container;
        return $this->view->render($this->response, $template, $vars)->withStatus($status_code);
    }
    
    /**
     * Return response with location header.
     *
     * @param string $location
     *
     * @return ResponseInterface
     */
    public function redirect( $location )
    {
        return $this->response->withHeader('Location', $location);
    }
    
    /**
     * Return 404 response.
     *
     * @return ResponseInterface
     */
    public function notFound()
    {
        return $this->notFoundHandler->__invoke($this->request, $this->response);
    }
    
    /**
     * Prepare JSON response.
     *
     * @param array $data
     * @param int   $status HTTP status code, default: null
     *
     * @return ResponseInterface
     */
    public function json(array $data,  $status = null)
    {
        $response = $this->preprocessResponse($data);
        if (!$status) {
            $status = (isset($response['error']['message']) ? $response['error']['message'] : null || isset($response['error']['fields'])? $response['error']['fields'] : null) ? 400 : 200;
        }
        
        return $this->response->withStatus($status)->withJson($response);
    }
    
    /**
     * Pre-process response data.
     *
     * @param array $data
     *
     * @return array
     */
    protected function preprocessResponse(array $data)
    {
        $response = $this->defaultResponse;
        if (isset($data['error']['message'])? $data['error']['message'] : null) {
            $response['error']['message'] = $data['error']['message'];
            unset($data['error']['message']);
        }
        if (isset($data['error']['fields'])? $data['error']['fields'] : null) {
            $response['error']['fields'] = $data['error']['fields'];
            unset($data['error']['fields']);
        }
        
        if (isset($data['error'])) {
            unset($data['error']);
        }
        
        $response['count'] = \count($data);
        $response['data'] = $data;
        
        return $response;
    }
}
