<?php
/**
 * Author: Shawn Chen
 * Desc: Basic Slimer framework Index controller
 */
namespace App\Controller;

class Index extends \Slimer\Controller
{
    public function indexAction()
    {
        return $this->render('index/index.html.twig');
    }
    
    public function loginAction()
    {
        if ($this->request->isGet()){
            if ($this->container->has('user') && $this->container->get('user')){
                return $this->response->withRedirect($this->router->pathFor('admin-admin'));            
            }else{
                $this->render('index/login.html.twig');
            }
        }else{
            //----check authen
            $account = $this->request->getParams()['username'];
            $password = $this->request->getParams()['password'];
            $user = $this->auth->login($account,$password);
            if ($user == null){
                $this->flash->addMessage('warning','username or password is not correct');
                return $this->response->withRedirect('/login');
            }else{
                return $this->response->withRedirect($this->router->pathFor('index'));
            }
        }
        
    }
    
    public function sbs_adminlte_sidebar_collapseAction()
    {
        $collapse = $this->request->getParams()['collapse'];
        $this->session->set('sbs_adminlte_sidebar_collapse',$collapse);
        return $this->json(['msg'=>'success']); 
    }
    
    public function logoutAction() {
        //----remove the session
        $this->session->destroy();
        return $this->response->withRedirect($this->router->pathFor('login'));
    }
    
    public function sbs_adminlte_user_profileAction() {
        return $this->render('index/profile.html.twig');
    }
    
    public function user_profile_passwordchangeAction(){
        $data = $this->request->getParsedBody();
        if (isset($data['newPassword'])){
            if ($this->user->get("type") === "local"){
                $this->dbGam->update("user",["password"=> \password_hash($data['newPassword'], PASSWORD_DEFAULT)],["id"=>$this->user->get("id")]);
            }else{
                return $this->json(["error"=>["message"=>"non local user type not allow to change the password"]],500);
            }
        }else{
            return $this->json(["error"=>["message"=>"wrong parameter, new password not found"]],400);
        }
        $this->logger->info("user={$this->user->get('loginName')} reset the password for self");
        return $this->json(["msg"=>"success"]);
    }
    
    public function introedAction(){
        $this->session->set('introed',true);
        return $this->json(["msg"=>"success"]);
    }
    
    public function unintroedAction(){
        $this->session->set('introed',false);
        return $this->response->withRedirect($this->router->pathFor('index'));
    }
    
    
    public function sbs_adminlte_show_taskAction( $args ) {
    }
    
    public function sbs_adminlte_all_tasks(){
    }
}