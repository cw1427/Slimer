<?php
/**
 * Author: Shawn Chen
 * Desc: Basic interface
 */

namespace Slimer\Auth\Repository;

use Slimer\Root;

interface RepositoryInterface
{
    /**
     * Get user by login.
     *
     * @return null|RepositoryInterface
     */
    public function getByLogin($login,$password);
    
    /**
     * Get login fields, eg: ['email', 'username'].
     *
     * @return array
     */
    public function getLoginFields();
    
    /**
     * Check if provided login and password are correct and return matched user
     * Otherwise, return null if no user found or password incorrect.
     *
     * @return null|Root
     */
    public function login($login, $password);
    
    /**
     * Generate special code for user who forgot password.
     *
     * @return string code
     */
    public function forgot($login);
    
    /**
     * Reset user password by code.
     *
     * @param string $code         Return value of self::forgot()
     * @param string $new_password New password for user
     *
     * @return bool
     */
    public function reset($code, $new_password);
}
