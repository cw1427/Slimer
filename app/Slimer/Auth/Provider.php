<?php
/**
 * Author: Shawn Chen
 * Desc: The authentication module extensition.
 */
namespace Slimer\Auth;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\Ldap\Entry;

class Provider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        
        $container['session'] = function (Container $container) {
            return new \SlimSession\Helper;
        };
        $container['session_middleware'] = function (Container $container) {
            return new \Slim\Middleware\Session($container['config']('session'));
//             return new \Slim\Middleware\SessionCookie();
        };
        $container['auth_middleware'] = function ($c) {
            return new \Slimer\Auth\Middleware\Auth($c);
        };
        $container['rbac_middleware'] = function ($c) {
            return new Middleware\RBAC($c);
        };
        $container['auth_repository'] = function ($c) {
            $class = $c['config']('auth.repository');
            
            return new $class($c);
        };
        $container['auth_storage'] = function ($c) {
            $class = $c['config']('auth.storage');
            
            return new $class($c);
        };
        $container['auth'] = function ($c) {
            return new \Slimer\Auth\Auth($c);
        };
        $container['user'] = function ($c) {
            return $c['auth']->getUser();
        };
        
        //@codeCoverageIgnoreStart
        if (\class_exists('\Symfony\Component\Ldap\Ldap')) {
            $container['ldap_client'] = $container->protect(function ($conf) use ($container) {
                $ldap = \Symfony\Component\Ldap\Ldap::create('ext_ldap', $container['config']($conf));
                return $ldap;
            });
            
            $container['ldap_entry'] = $container->protect(function ($dn, array $attributes = array()) use ($container) {
                return new Entry($dn,$attributes);
            });
        }
        // if Slimer/orm not installed, we use \Slimer\Root class directly
        if (!\class_exists('\Slimer\ORM\Entity')) {
            $container['entity'] = $container->protect(function ($name) use ($container) {
                return new \Slimer\Root($container);
            });
        }
        //@codeCoverageIgnoreEnd
        
        $container['httpauth_middleware'] = function ($c) {
            return new \Slim\Middleware\HttpBasicAuthentication([
                "path"=> $c["config"]('suit.httpauth.path') ? $c["config"]("suit.httpauth.path") : ["/"],
                "paththrough"=> $c["config"]('suit.httpauth.paththrough'),
                "authenticator" =>  new \Slimer\Auth\MixDBLdapAuthenticator(["container"=>$c])
            ]);
            
        };
        
        //----PhpRbac
        $container['rbac'] = function($c) {
            return new Rbac('',$c);
        };
    }
}
