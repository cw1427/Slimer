<?php
/**
 * Author: Shawn Chen
 * Show all of the built-in cmd
 */
namespace App\Command;


Class Cmd extends GenericCommand{
    
    protected  $desc = "List current supported Commands names";
    protected  $params = ["--list|-l" => "List all of the commands"
    ];
    
    protected $out=[];

    public function _command($args)
    {
        if (isset($args["list"]) || isset($args["l"])){
            $cmds = $this->container['config']('suit.commands');
            foreach ($cmds as $cmd=>$path){
                $cmdObj = new $path($this->container);
                \array_push($this->out,"Command Name: {$cmd}");
                \array_push($this->out,"{$cmdObj->command(["","-h"])}");
                \array_push($this->out,PHP_EOL . PHP_EOL);
            }
            return \implode(PHP_EOL,$this->out);
        }else{
            throw new \Exception("Command error, wrong parameter.",400);
        }
        
    }
}