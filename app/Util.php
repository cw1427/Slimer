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
        $ldap = $this->container['ldap_client']('auth.ldap.server');
        $ldap->bind($this->container['config']('auth.ldap.admin.dn'), $this->container['config']('auth.ldap.admin.password'));
        $collection = $ldap->query($this->container['config']('auth.ldap.baseDN'), '(' . 'motguid=' . $coreid .')')->execute();
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
        $message->setFrom($mailFrom);
        if (\is_array($maileTo)){
            foreach ($maileTo as $to){
                $message->addTo($to);
            }
        }else{
            $message->addTo($maileTo);
        }
        if ($mailCc != null) {
            if (\is_array($mailCc)){
                foreach ($mailCc as $cc){
                    $message->addCc($cc);
                }
            }else{
                $message->addCc($mailCc);
            }
        }
        $message->setSubject($subject);
        $message->setHTMLBody($content);
        //$message->addEmbeddedFile(APP_PATH . DS . 'Static' . DS . 'img' . DS . 'spacer.gif');
        $this->container['smtpMailer']->send($message);
    }
    
    public function gerritCheckByCmd($id,$server='test'){
        $cmd = 'ssh -l %s -p %d -i "%s" %s "gerrit gsql --format pretty -c \"select e.external_id from accounts a,account_external_ids e where a.account_id=e.account_id and e.external_id=' . "'username:$id'" . ' and a.inactive=' . "'Y'". '\"" |  wc -l';
        $gerConf = $this->container['config']('gerrit.'.$server);
        $SKEY = APP_ROOT . DS . 'Data' . DS . 'gerrit_id_rsa';
        $cmd = sprintf($cmd,$gerConf['gamuser'],$gerConf['sshport'],$SKEY, $gerConf['host']);
        $command = $this->container['shellCommand'](['command'=>$cmd,'useExec'=>true]);
        if ($command->execute()) {
            $result=$command->getOutput();
            if ((int)$result > 3){
                return false;
            }else{
                return true;
            }
        } else {
            $exitCode = $command->getExitCode();
            $this->container->logger->error( $command->getError() . ' error code=' . $exitCode);
            return false;
        }
        
    }
    
}