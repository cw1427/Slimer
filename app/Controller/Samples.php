<?php
/**
 * Author: Shawn Chen
 * Desc: Sample module controller
 */

namespace App\Controller;

use function GuzzleHttp\json_encode;

/**
 * Admin Controler for the GAM admin component.
 *
 * That controller handles work with auth for you
 */
class Samples extends \Slimer\Controller
{
    
    public function formAction(){
        //$this->logger->debug('test add debug message');
        //$this->flash->addMessageNow('success','success');
        //$this->flash->addMessage('warning','warning in next request');
        return $this->render('samples/form.html.twig',['al'=>['key1'=>'value1','key2'=>'value2']]);
    }
    
    public function form_submitAction(){
        $newCoreid = $this->request->getParams()['newCoreid'];
        $newinput = $this->request->getParams()['newinput'];
        $reasonText = $this->request->getParams()['reasonText'];
        $this->flash->addMessage('success',"{$newCoreid}-{$newinput}-{$reasonText}");
        return $this->response->withRedirect($this->router->pathFor('samples-form'));
    }
    
    public function tableAction(){
        return $this->render('samples/table.html.twig');
    }
    
    public function table_listAction(){
        $params = $this->request->getParams();
        $offset = $params['offset'];
        $limit = $params['limit'];
        $search = isset($params['search'])? $params['search']: null;
        $WHERE = [];
        if (isset($search) && $search !=""){
            $WHERE['OR']=["name[~]"=>"%{$search}%","country[~]"=>"%{$search}%"];
        }
        $total = $this->dbDefault->count("sample",$WHERE);
        $WHERE['LIMIT']=[$offset,$limit];
        $tl=$this->dbDefault->select("sample","*",$WHERE);
        return $this->response->withStatus(200)->withJson(["rows"=>$tl,"total"=>$total]);
    }
    
    public function table_addAction(){
        $params = $this->request->getParams();
        $params['name'] = trim($params['name']," ");
        $params['country'] = trim($params['country']," ");
        $this->dbDefault->insert('sample', $params);
        $this->flash->addMessage('success',"Successfully add a new data {$params['name']}");
        return $this->response->withRedirect($this->router->pathFor('samples-table'));
    }
    
    public function table_deleteAction(){
        $params = $this->request->getParsedBody();
        if (isset($params["id"])){
            $this->dbDefault->delete("sample",["id"=>$params['id']]);
            $this->flash->addMessage('success',"Successfully delete {$params['name']}");
            return $this->json(["message"=>"successfully deleted"]);
        }else{
            return $this->json(["error"=>["message"=>"wrong parameter"]],400);
        }
    }
    
}