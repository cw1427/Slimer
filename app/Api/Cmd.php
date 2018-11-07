<?php
/**
 * Author: Shawn Chen
 * Desc: Admin module controller
 */

namespace App\Api;

use function GuzzleHttp\json_encode;

/**
 * Admin Controler for the GAM admin component.
 *
 * That controller handles work with auth for you
 */
class Cmd extends \App\Api\Api
{
    public function dbinitAction()
    {
        $ct = $this->request->getContentType();
        if ($ct !== "application/json"){
            return $this->json(['error'=>['message'=>'wrong request content type,','code'=>400],
                'tip'=>'please add Content-Type=application/json in the header']);
        }else{
            $data = $this->request->getParsedBody();
            if (isset($data["sync"]) && $data["sync"]==1 && isset($data['dbEngine'])){
                $cmd = new \App\Command\Dbinit($this->container);
                $res = $cmd->_command($data);
                return $this->json(['message'=>'success','resDdata'=>$res]);
            }else{
                return $this->json(["error"=>["message"=>"Wrong parameter","code"=>400,"fields"=>["sync","dbEngine"]],
                    "tip"=>"sync and dbEngine parameter are necessary"]);
            }
        }
    }
    
}