<?php
/**
 * Author: Shawn Chen
 * Init DB table and basic data records helper command
 */
namespace App\Command;


Class RbacInit extends GenericCommand{
    
    protected  $desc = "Basic RBAC tables structure init, it will depends owasp/phprbac library";
    protected  $params = ["--sql_path" => "The RBAC tables init sql path, the default=<vendor>/owasp/phprbac/PhpRbac/database/mysql.sql",
                          "--dbEngine" => "The name of db engine in this App"
    ];

    public function _command($args)
    {
        if (isset($args["dbEngine"])){
            $args["sql_path"] = isset($args["sql_path"]) ? $args["sql_path"] : APP_ROOT . DS . 'vendor/owasp/phprbac/PhpRbac/database/mysql.sql';
            $args["prefix"] = $this->container['config']('rbac.rbac_table_prefix') ? $this->container['config']('rbac.rbac_table_prefix') : 'PREFIX_';
            return $this->_execute($args);
        }else{
            throw new \Exception("Command error, wrong parameter.",400);
        }
        
    }
    
    private function _execute($args){
        $engine = $this->container[$args["dbEngine"]];
        if (\is_file($args["sql_path"])){
            $res = $engine->query(\sprintf("SHOW TABLES LIKE '%s'", $args["prefix"] . "permissions" ))->fetchAll();
            if (isset($res) && \count($res) > 0){
                return "basic RBAC tables already exists ignore." . PHP_EOL;
            }else{
                $sql_ddl = \file_get_contents($args["sql_path"]);
                $sql_ddl = str_replace("PREFIX_", $args["prefix"], $sql_ddl);
                $engine->query($sql_ddl);
                return "Successfully created RBAC tables" . PHP_EOL;
            }
        }
    }

}