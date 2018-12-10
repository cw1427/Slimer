<?php
/**
 * Author: Shawn Chen
 * Desc: Admin module controller
 */

namespace App\Api;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * The API type basic controller to overwrite the basci Action response the json type data
 */
class Api extends \Slimer\Controller
{
    protected $defaultResponse = [
        'error' => [
            'message' => null,
            'fields' => [],
            'code' => 400
        ],
        'count' => 0,
        'data' => [],
    ];
    
    
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $this->request = $request;
        $this->response = $response;
        if ($request->getAttribute('route')) {
            $name = \explode('-', $request->getAttribute('route')->getName());
            $action = \strtolower(\end($name)).'Action';
            
            if (\method_exists($this, $action)) {
                try {
                    return \call_user_func([$this, $action],$args);
                } catch (\Exception $e) {
                    return $this->errorHandle($e);
                }
            }
            
            return $this->notFound();
        }
        
        return $this->notFound();
    }
    
    
    protected function preprocessResponse(array $data)
    {
        $response = $this->defaultResponse;
        if (isset($data['error'])){
            if (isset($data['error']['message'])? $data['error']['message'] : null) {
                $response['error']['message'] = $data['error']['message'];
                unset($data['error']['message']);
            }
            if (isset($data['error']['fields'])? $data['error']['fields'] : null) {
                $response['error']['fields'] = $data['error']['fields'];
                unset($data['error']['fields']);
            }
            if (isset($data['error']['code'])? $data['error']['code'] : null) {
                $response['error']['code'] = $data['error']['code'];
                unset($data['error']['code']);
            }
            unset($data['error']);
        }else{
            unset($response['error']);
        }
        
        $response['count'] = \count($data);
        $response['data'] = $data;
        
        return $response;
    }
    
    /*
     * overwrite the notFound function response the json info
     */
    public function notFound()
    {
        $data=["error"=>["code"=>404,"message"=>$this->request->getUri()->__toString() . " not found"]];
        return $this->json($data,404);
    }
    
    public function errorHandle(\Exception $e){
        $this->logger->error($this->request->getUri()->__toString(), ['code' => 500, 'exception' => $e]);
        $message = $this->config('suit.settings.displayErrorDetails') ? $e->__toString() : $e->getMessage();
        $data=["error"=>["code"=>500,"message"=>$message]];
        return $this->json($data,500);
    }

}