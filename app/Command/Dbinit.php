<?php
/**
 * Author: Shawn Chen
 * Init DB table and basic data records helper command
 */
namespace App\Command;


Class Dbinit extends GenericCommand{
    
    protected  $desc = "Basic DB init command, will check if the user table exists or not and help to reset the admin account";
    protected  $params = ["--sync" => "Check if user table exists if not then help to create it",
                          "--dbEngine" => "The name of db engine in this App"
    ];

    public function _command($args)
    {
        if (isset($args["sync"]) && isset($args["dbEngine"])){
            $this->_scan($args);
            return "successfully execute Dbinit cmd.";
        }else{
            throw new \Exception("Command error, wrong parameter.",400);
        }
        
    }
    
    private function _scan($args){
        $dataPath = APP_ROOT . DS . 'Data';
        $engine = $this->container[$args["dbEngine"]];
        foreach (\scandir($dataPath) as $item){
            $is = \explode('.',$item);
            if (\is_file($dataPath . DS . $item) && \end($is) == 'sql'){
                $table_name = $is[0];
                $res = $engine->query(\sprintf("SHOW TABLES LIKE '%s'",$table_name))->fetchAll();
                if (isset($res) && \count($res) > 0){
                    print "basic table " . $table_name . " already exists ignore." . PHP_EOL;
                }else{
                    $sql_ddl = \file_get_contents($dataPath . DS . $item, FILE_USE_INCLUDE_PATH );
                    $engine->query($sql_ddl);
                    print "Successfully created table " . $table_name . PHP_EOL;
                }
            }
        }
    }

}