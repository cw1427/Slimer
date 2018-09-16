<?php
/**
 * Author: Shawn Chen
 * Desc: Basic Slimer framework Index controller
 */
namespace App\Controller;

class Index extends \App\Controller
{
    public function indexAction()
    {
        return $this->render('index/index.html.twig');
    }
    
    public function secondAction()
    {
        return $this->response->write('Hello, second world!');
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
                //----put user into session
                $this->session->set('user',$user->getData());
                return $this->response->withRedirect($this->router->pathFor('admin-admin'));
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
    
    public function profileAction() {
        return $this->response->write('profile');
    }
}