<?php
/**
 * Author: Shawn Chen
 * Desc: A LDAP repository implementor which also support for the local user type authenticaton.
 */

namespace Slimer\Auth\Repository;

use Psr\Container\ContainerInterface;
use Slimer\Root;

class LDAP extends Root implements RepositoryInterface
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        //@codeCoverageIgnoreStart
        if (!\class_exists('\Symfony\Component\Ldap\Ldap')) {
            throw new \Exception('symfony/ldap package required for ldap auth');
        }
        //@codeCoverageIgnoreEnd
    }
    
    /**
     * {@inheritdoc}
     */
    public function getLoginFields()
    {
        return $this->config('auth.ldap.fields.login', ['uid', 'mail']);
    }
    
    public function getPasswordField()
    {
        return 'password';
    }
    
    /**
     * {@inheritdoc}
     */
    public function login( $login,  $password)
    {
        //----checking the account in local DB first
        $user = $this->entity($this->config('auth.entity'))->load($login, $this->config('auth.ldap.fields.loginInDb', 'loginName'));
        if ($user !=null && $user->get('type') === 'local'){
            //----local user just checking the password
            if (!\password_verify($password, $user->get($this->getPasswordField()))) {
                return null;
            }
        }else{
            $user = $this->getByLogin($login,$password);
        }
        return $user;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getByLogin( $login,$password)
    {
        $query = '(|';
        foreach ($this->getLoginFields() as $field) {
            $query .= '('.$field.'='.$login.')';
        }
        $query .= ')';
        try {
            $ldap = $this->ldap_client('auth.ldap.server');
            $ldap->bind(sprintf($this->config('auth.ldap.userDn'),$login), $password);
        }catch (\Exception $t) {
            $this->logger->error('LDAP bind failed', ['loginName' =>$login]);
            return null;
        }
        $collection = $ldap->query($this->config('auth.ldap.baseDN'), $query)->execute();
        foreach ($collection->toArray() as $entry) {
            $user = $this->entity($this->config('auth.entity'))->load(strtolower($entry->getAttributes()['motCoreID'][0]), $this->config('auth.ldap.fields.loginInDb', 'loginName'));
            foreach ($entry->getAttributes() as $attribute => $value) {
                $field = $this->config('auth.ldap.fields.map.'.$attribute);
                if ($field) {
                    $user->set($field, isset($value[0])? $value[0] :null);
                }
            }
            $user->set($this->config('auth.ldap.fields.loginInDb', 'loginName'),strtolower($login));
            $user->set('type','ldap');
            $data = $user->getData();
            $user->save();
            $user->setData($data);
            return $user;
        }
        
        return null;
    }
    
    /**
     * {@inheritdoc}
     */
    public function forgot( $login)
    {
        return '';
    }
    
    /**
     * {@inheritdoc}
     */
    public function reset( $code,  $new_password)
    {
        return false;
    }
}