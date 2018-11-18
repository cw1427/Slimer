<?php
/**
 * Author: Shawn Chen
 * Desc: The authentication function provider
 */
namespace Slimer\Auth;

use Slimer\Root;

class Auth extends Root
{
    /**
     * Log in user.
     *
     * Return result, based by selected session storage
     *
     * @param string $login
     * @param string $password
     *
     * @return mixed
     */
    public function login( $login,  $password)
    {
        $user = $this->auth_repository->login($login, $password);
        
        if (null === $user) {
            return null;
        }
        
        if ($this->container->has('user')) {
            unset($this->container['user']);
        }
        $user->updateLogin();
        //----add user group info into current user
        $gs = $user->getUserGroups();
        $permGroupIds = [];
        foreach ($gs as $group){
            \array_push($permGroupIds,$group['ID']);
        }
        $rs = $user->getUserRoles($permGroupIds);
        $user->set('perm_group',$gs);
        $user->set('roles',$rs);
        $this->container['user'] = $user;
        
        return $this->auth_storage->setUser($user);
    }
    
    /**
     * Generate special code for user who forgot password.
     *
     * @return string code
     */
    public function forgot($login)
    {
        return $this->auth_repository->forgot($login);
    }
    
    /**
     * Reset user password by code.
     *
     * @param string $code         Return value of self::forgot()
     * @param string $new_password New password for user
     *
     * @return bool
     */
    public function reset( $code, $new_password)
    {
        return $this->auth_repository->reset($code, $new_password);
    }
    
    /**
     * Check if current user is logged in.
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->auth_storage->isLoggedIn();
    }
    
    /**
     * Get current user.
     *
     * @return null|Root
     */
    public function getUser()
    {
        return $this->auth_storage->getUser();
    }
    
    public function logout()
    {
        $this->auth_storage->logout();
    }
}