<?php


namespace App\Controller;

/**
 * Auth.
 *
 * That controller handles work with auth for you
 */
class Auth extends \Slimer\Controller
{
    public function formAction()
    {
        return $this->render('auth/form.html', [
            'resetCode' => $this->request->getAttribute('reset_code'),
        ]);
    }
    
    public function loginAction()
    {
//         $this->flash->addMessage('danger','flash message in auth-long action');
        return $this->response->withRedirect('/login');
    }
    
    public function registerAction()
    {
        $data = $this->request->getParsedBody();
        try {
            $this->entity('user')->register($data);
        } catch (\Exception $t) {
            $this->flash->addMessage('danger', $t->getMessage());
            
            return $this->redirect('/auth');
        }
        
        $this->auth->login($data['login'], $data['password']);
        
        return $this->redirect('/');
    }
    
    /**
     * Logout current user, GET without any params.
     */
    public function logoutAction()
    {
        $this->auth->logout();
        
        return $this->redirect('/auth');
    }
    
}