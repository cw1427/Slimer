<?php
/**
 * Author: Shawn Chen
 * Desc: The Database base uer repository implementer 
 */
namespace Slimer\Auth\Repository;

use Slimer\Root;

class Db extends Root implements RepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getLoginFields()
    {
        return ['loginname','username','email'];
    }
    
    /**
     * Get password field, eg: 'password'.
     *
     * @return string
     */
    public function getPasswordField()
    {
        return 'password';
    }
    
    /**
     * Get forgot password code field, eg: 'forgot'.
     *
     * @return string
     */
    public function getForgotField()
    {
        return 'forgot';
    }
    
    /**
     * {@inheritdoc}
     */
    public function login( $login,  $password)
    {
        $user = $this->getByLogin($login,$password);
        if (null === $user) {
            return null;
        }
        
        if (!\password_verify($password, $user->get($this->getPasswordField()))) {
            return null;
        }
        
        return $user;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getByLogin($login,$password)
    {
        $entity = $this->entity($this->config('auth.entity', 'user'));
        foreach ($this->getLoginFields() as $field) {
            try {
                if ($entity->has([$field => $login])) {
                    return $entity->load($login, $field);
                }
            } catch (\Exception $t) {
                //If field does not exist, exception will be thrown,
                //but for that case it's not a problem,
                //so just ignore it and go to the next field
            }
        }
        
        return null;
    }
    
    /**
     * {@inheritdoc}
     */
    public function forgot( $login)
    {
        $user = $this->getByLogin($login);
        if (null === $user) {
            return '';
        }
        
        $user->set($this->getForgotField(), \md5($user->getId().\random_int(PHP_INT_MIN, PHP_INT_MAX)))->save(false);
        
        return $user->get($this->getForgotField());
    }
    
    /**
     * {@inheritdoc}
     */
    public function reset( $code,  $new_password)
    {
        $user = $this->entity($this->config('auth.entity', 'user'));
        if (!$user->has([$this->getForgotField() => $code])) {
            return false;
        }
        
        $user->load($code, $this->getForgotField())
        ->setData([
            $this->getForgotField() => null,
            $this->getPasswordField() => \password_hash($new_password, PASSWORD_DEFAULT),
        ])->save(false);
        
        return true;
    }
}