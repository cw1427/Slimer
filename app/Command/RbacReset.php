<?php
/**
 * Author: Shawn Chen
 * Init DB table and basic data records helper command
 */
namespace App\Command;


Class RbacReset extends GenericCommand{
    
    protected  $desc = "RBAC initial data reset including the config basic permission data, it will depends owasp/phprbac library";
    protected  $params = ["--force" => "need to explict force reset the rbac structure"
    ];
    
    protected $out=[];
    
    public function _command($args)
    {
        if (isset($args["force"])){
            $rbac = $this->container->get('rbac');
            if ($rbac->reset(true)){
                \array_push($this->out, "Successfully reset the RBAC tables (rolepermissions,roles,permissions,userroles)");
                $paths = $this->container['config']('rbac.rbac_role_path');
                if  ($paths !=null) {
                    foreach ($paths as $path=>$desc){
                        $this->rbac->Roles->addPath($path,$desc);
                    }
                    \array_push($this->out, "Successfully init the roles from rbac.rbac_role_path config");
                }
                $paths = $this->container['config']('rbac.rbac_perm_path');
                if  ($paths !=null) {
                    foreach ($paths as $path=>$desc){
                        $this->rbac->Permissions->addPath($path,$desc);
                    }
                    \array_push($this->out, "Successfully init the permissions from rbac.rbac_perm_path config");
                }
                $paths = $this->container['config']('rbac.rbac_role_perm');
                if  ($paths !=null) {
                    foreach ($paths as $role=>$perms){
                        foreach ($perms as $perm){
                            $this->rbac->assign($role,$perm);
                        }
                    }
                    \array_push($this->out, "Successfully init the rolepermissions from rbac.rbac_role_perm config");
                }
                return implode(PHP_EOL, $this->out);
            }
            return "Reset RBAC tables failed";
        }else{
            throw new \Exception("Command error, wrong parameter.",400);
        }
        
    }

}