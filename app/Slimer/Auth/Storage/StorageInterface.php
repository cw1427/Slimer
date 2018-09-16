<?php

namespace Slimer\Auth\Storage;

use Slimer\Root;

interface StorageInterface
{
    /**
     * Set user data to storage.
     *
     * @param Root $user
     *
     * @return mixed
     */
    public function setUser(Root $user);
    
    /**
     * Get current user from storage.
     *
     * @return null|Root
     */
    public function getUser();
    
    /**
     * Check if current user logged in.
     *
     * @return bool
     */
    public function isLoggedIn();
    
    /**
     * Log out current user.
     */
    public function logout();
}
