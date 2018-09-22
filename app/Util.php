<?php
/**
 * Author: Shawn Chen
 * Desc: common util 
 */
namespace App;

class  Util
{
    
    protected  $container;
    
    public function __construct()
    {
    
    }
    
    public  function ldapCheck($coreid){
        $ldap = $this->container->ldap_client;
        $ldap->bind($this->container['config']('auth.ldap.admin.dn'), $this->container['config']('auth.ldap.admin.password'));
        $collection = $this->container->ldap_client->query($this->container['config']('auth.ldap.baseDN'), '(' . 'motguid=' . $coreid .')')->execute();
        return $collection;
    }
    
    public  function setContainer($c){
        $this->container = $c;
    }
    
    public function sendMail($mailFrom,$maileTo, $mailCc,$subject,$content){
        if ($mailFrom == null){
            $mailFrom = '<your maile from address>';
        }
        $message = $this->container['smtpMessage'];
        $message->setFrom($mailFrom)->addTo($maileTo);
        if ($mailCc != null) {
            $message->addCc($mailCc);
        }
        $message->setSubject($subject);
        $message->setHTMLBody($content);
        $message->addEmbeddedFile(APP_PATH . DS . 'Static' . DS . 'img' . DS . 'spacer.gif');
        $this->container['smtpMailer']->send($message);
    }
    
}