<?php
/**
 * Author: Shawn Chen
 * Init DB table and basic data records helper command
 */
namespace App\Command;


Class RbacInit extends GenericCommand{
    
    protected  $desc = "Basic RBAC tables structure init, it will depends owasp/phprbac library";
    protected  $params = ["--sql_path" => "The RBAC tables init sql path, the default=<vendor>/owasp/phprbac/PhpRbac/database/mysql.sql",
                          "--dbType"=>['default'=>'mysql','desc'=>"Provide the current db type, default is mysql,: mysql|sqlite|pgsql"],
                          "--dbEngine" => "The name of db engine in this App"
    ];

    public function _command($args)
    {
        if (isset($args["dbEngine"])){
            switch ($args['dbType']){
                case "mysql":
                    $args["sql_path"] = isset($args["sql_path"]) ? $args["sql_path"] : APP_ROOT . DS . 'vendor/owasp/phprbac/PhpRbac/database/mysql.sql';
                    break;
                case "sqlite":
                    $args["sql_path"] = isset($args["sql_path"]) ? $args["sql_path"] : APP_ROOT . DS . 'vendor/owasp/phprbac/PhpRbac/database/sqlite.sql';
                    break;
                default:
                    print "unsupport dbType right now";
                    return;
            }
            $args["prefix"] = $this->container['config']('rbac.rbac_table_prefix') ? $this->container['config']('rbac.rbac_table_prefix') : 'PREFIX_';
            return $this->_execute($args);
        }else{
            throw new \Exception("Command error, wrong parameter.",400);
        }
        
    }
    
    private function _execute($args){
        $engine = $this->container[$args["dbEngine"]];
        if (\is_file($args["sql_path"])){
            switch ($args['dbType']){
                case "mysql":
                    $res = $engine->query(\sprintf("SHOW TABLES LIKE '%s'", $args["prefix"] . "permissions" ))->fetchAll();
                    break;
                case "sqlite":
                    $res = $engine->query("SELECT name FROM sqlite_master WHERE type='table' and name='{$args['prefix']}permissions'")->fetchAll();
                    break;
                default:
                    print "unsupport dbType right now";
                    return;
            }
            
            if (isset($res) && \count($res) > 0){
                return "basic RBAC tables already exists ignore." . PHP_EOL;
            }else{
                $sql_ddl = \file_get_contents($args["sql_path"]);
                $sql_ddl = str_replace("PREFIX_", $args["prefix"], $sql_ddl);
                $ddls = explode(";", $sql_ddl);
                foreach ($ddls as $ddl){
                    $engine->query($ddl);
                }
                return "Successfully created RBAC tables" . PHP_EOL;
            }
        }
    }

}