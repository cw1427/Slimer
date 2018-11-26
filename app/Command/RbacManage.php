<?php
/**
 * Author: Shawn Chen
 * Provide the RBAC metadata management: Role,Permission,PermissionRoles
 */
namespace App\Command;


Class RbacManage extends GenericCommand{
    
    protected  $desc = "Basic RBAC tables structure init, it will depends owasp/phprbac library";
    protected  $params = ["--role" => "The role title you want to manage",
        "--type" => "The manage type, for now only support for add|delete|assign|unassign|show",
        "--desc" => "The description",
        "--perm" => "The perm title you want to manage",
        "--parent" => "The parent title or path what you want to manage belong to",
        "--recurisive" => "determind if to remove all of the descendant",
        "--group" => "The role assigne to a specific groupName or groupId",
        "--dbEngine" => "Once you put the groupName it would be need to search its id"
    ];
    
    protected $out=[];
    
    public function _command($args)
    {
        if (isset($args["role"]) || isset($args["perm"])){
            if (isset($args["role"])){
                if (isset($args["type"])){
                    if ($args["type"] === 'add'){
                        $this->__add($args,'role');
                    }else if ($args["type"] === 'delete'){
                        $this->__delete($args,'role');
                    }else if($args["type"] === 'assign' && isset($args["perm"])){
                        $this->rbac->assign($args["role"],$args["perm"]);
                        \array_push($this->out, "Successfully assing perm=" . $args["perm"] . " on the role=" . $args["role"]);
                    }else if($args["type"] === 'assign' && isset($args["group"])){
                        if (\is_numeric($args["group"])){
                            $this->rbac->Users>assign($args["role"],$args["group"]);
                        }else{
                            if(isset($args["dbEngine"])){
                                $engine = $this->container[$args["dbEngine"]];
                                $res = $engine->select("perm_group","ID",["Name"=>$args["group"]]);
                                if (isset($res) && is_array($res) && count($res)==1){
                                    $this->rbac->Users->assign($args["role"],$res[0]);
                                }else{
                                    throw new \Exception("Wrong usergroup name input",400);
                                }
                            }else{
                                throw new \Exception("Please also input the dbEngin name which u used in your App",400);
                            }
                        }
                        \array_push($this->out, "Successfully assing role=" . $args["role"] . " on the group=" . $args["group"]);
                    }else if($args["type"] === 'unassign' && isset($args["perm"])){
                        $this->rbac->Roles->unassign($args["role"],$args["perm"]);
                        \array_push($this->out, "Successfully unassing perm=" . $args["perm"] . " on the role=" . $args["role"]);
                    }else if($args["type"] === 'unassign' && isset($args["group"])){
                        if (\is_numeric($args["group"])){
                            $this->rbac->Users>unassign($args["role"],$args["group"]);
                        }else{
                            if(isset($args["dbEngine"])){
                                $engine = $this->container[$args["dbEngine"]];
                                $res = $engine->select("perm_group","ID",["Name"=>$args["group"]]);
                                if (isset($res) && is_array($res) && count($res)==1){
                                    $this->rbac->Users->unassign($args["role"],$res[0]);
                                }else{
                                    throw new \Exception("Wrong usergroup name input",400);
                                }
                            }else{
                                throw new \Exception("Please also input the dbEngin name which u used in your App",400);
                            }
                        }
                        \array_push($this->out, "Successfully unassing role=" . $args["role"] . " on the group=" . $args["group"]);
                    }else if($args["type"] === 'show'){
                        $res = $this->rbac->showRoleTree();
                        foreach ($res as $r){
                            \array_push($this->out, $r['name']);
                        }
                        \array_push($this->out, "Successfully show the hierachical tree on the role=" . $args["role"]);
                    }else{
                        throw new \Exception("Command error, wrong parameter, you should put at least --type <add|delete|assign|unassign|show>",400);
                    }
                }else{
                    throw new \Exception("Command error, wrong parameter, you should put at least --type <add|delete|assign|unassign|show>",400);
                }
                return implode(PHP_EOL, $this->out);
            }else if(isset($args["perm"])){
                if (isset($args["type"])){
                    if ($args["type"] === 'add'){
                        $this->__add($args,'perm');
                    }else if ($args["type"] === 'delete'){
                        $this->__delete($args,'perm');
                    }else if($args["type"] === 'show'){
                        $res = $this->rbac->showPermTree();
                        foreach ($res as $r){
                            \array_push($this->out, $r['name']);
                        }
                        \array_push($this->out, "Successfully show the hierachical tree on the permission=" . $args["perm"]);
                    }else{
                        throw new \Exception("Command error, wrong parameter, you should put at least --type <add|delete|show>",400);
                    }
                }else{
                    throw new \Exception("Command error, wrong parameter, you should put at least --type <add|delete|show>",400);
                }
                return implode(PHP_EOL, $this->out);
            }
        }else{
            throw new \Exception("Command error, wrong parameter, you should put --role or --perm.",400);
        }
    }
    
    protected function __add($args,$type){
        if (isset($args['parent'])){
            if (substr ($args['parent'], 0, 1) == "/"){
                $parentId = $this->rbac->{$type==="role"?"Roles":"Permissions"}->pathId($args['parent']);
            }else{
                $parentId = $this->rbac->{$type==="role"?"Roles":"Permissions"}->titleId($args['parent']);
            }
        }else{
            $parentId = null;
        }
        $id = $this->rbac->{$type==="role"?"Roles":"Permissions"}->add($args[$type],isset($args["desc"]) ? $args["desc"] : $args[$type],$parentId);
        \array_push($this->out, "Successfully add, Id=" . $id);
    }
    
    protected function __delete($args,$type){
        if ($args[$type] === 'root' || $args[$type] === '/root' || $args[$type] ==1){
            throw new \Exception("Root is not been allowed deleted",400);
        }else{
            $recurisive = isset($args["recurisive"])? true:false;
            if (substr ($args[$type], 0, 1) == "/"){
                $Id = $this->rbac->{$type==="role"?"Roles":"Permissions"}->pathId($args[$type]);
            }else{
                $Id = $this->rbac->{$type==="role"?"Roles":"Permissions"}->titleId($args[$type]);
            }
            if (isset($Id)){
                $this->rbac->{$type==="role"?"Roles":"Permissions"}->remove($Id,$recurisive);
                \array_push($this->out, "Successfully remove " . $args[$type]);
            }else{
                throw new \Exception("Wrong params. not found",400);
            }
        }
    }

}