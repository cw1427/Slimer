<?php

namespace  Slimer\Auth;

use Slim\Middleware\HttpBasicAuthentication\AuthenticatorInterface;

/*
 * Author: Shawn Chen
 */


class MixDBLdapAuthenticator implements AuthenticatorInterface
{
    private $options;
    
    public function __construct(array $options = [])
    {
        
        /* Default options. */
        $this->options = [
            "table" => "users"
        ];
        
        if ($options) {
            $this->options = array_merge($this->options, $options);
        }
    }
    
    public function __invoke(array $arguments)
    {
        $user = $arguments["user"];
        $password = $arguments["password"];
        
        $user = $this->options["container"]->auth->login($user,$password);
        if ($user != null){
            $this->options["container"]["basicAuth"]=true;
            return true;
        }
        return false;
    }
    
}
