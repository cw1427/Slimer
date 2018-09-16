<?php
/**
 * Author: Shawn Chen
 * Desc: PHP Session basic storage implementor
 */

namespace Slimer\Auth\Storage;

use Slimer\Root;

class Session extends Root implements StorageInterface
{
    /**
     * {@inheritdoc}
     */
    public function setUser(Root $user)
    {
        if (PHP_SESSION_ACTIVE !== \session_status()) {
            throw new \Exception('Session not started');
        }
        
        $_SESSION['user'] = $user->getData();
        if (isset($_SESSION['user']['password'])) {
            unset($_SESSION['user']['password']);
        }
        
        return $user;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getUser()
    {
        if (PHP_SESSION_ACTIVE !== \session_status()) {
            return null;
        }
        
        if (isset($_SESSION['user']) ? $_SESSION['user'] :  null) {
            return $this->entity($this->config('auth.entity'))->setData($_SESSION['user']);
        }
        
        return null;
    }
    
    /**
     * {@inheritdoc}
     */
    public function isLoggedIn()
    {
        if (PHP_SESSION_ACTIVE !== \session_status()) {
            return false;
        }
        
        return (bool) (isset($_SESSION['user']) ? $_SESSION['user'] : null);
    }
    
    /**
     * {@inheritdoc}
     */
    public function logout()
    {
        $_SESSION = [];
        if (PHP_SESSION_ACTIVE === \session_status()) {
            \session_destroy();
        }
    }
}