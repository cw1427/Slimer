<?php
namespace App\Command;

use Slimer\Root;

/**
 * Author: Shawn Chen
 * 
 */

Abstract Class GenericCommand extends Root{
    
    protected  $desc = "Generic command class";
    protected  $params = ["--help|-h" => "list the command help description"];
    
    
    public function command($args)
    {
        \array_unshift($args,'');
        $parseArgs = \CommandLine::parseArgs($args);
        if (isset($parseArgs["help"]) || isset($parseArgs["h"])){
            $out ="Description: " . $this->desc . "\nAvaliable parameter list:";
            foreach ($this->params as $k => $v){
                $desc = is_array($v) ? $v['desc']:$v;
                $out = $out . "\n" . "{$k} : {$desc}";
            }
            return $out;
        }else if ($this->_checkParameters($parseArgs)){
            return $this->_command($parseArgs);
        }else{
            return "Error: parameter not matched \n, please input --help or -h to check the detail paramter list";
        }
    }
    
    protected function _checkParameters(&$parseArgs){
        foreach ($this->params as $arg=>$desc){
            $args = \explode("|",$arg);
            foreach ($args as $a){
                if (\strpos($a,'--') === 0){
                    $a = \substr($a,2);
                }else if (\strpos($a,'-') === 0){
                    $a = \substr($a,1);
                }
                if (array_key_exists($a, $parseArgs)){
                    return true;
                }
            }
            // no parameter, check if has default
            if (is_array($desc) && isset($desc['default'])){
                $args = \explode("|",$arg);
                foreach ($args as $a){
                    if (\strpos($a,'--') === 0){
                        $a = \substr($a,2);
                    }else if (\strpos($a,'-') === 0){
                        $a = \substr($a,1);
                    }
                    $parseArgs[$a] = $desc['default'];
                }
                return true;
            }
        }
        return false;
    }
    
     
    abstract public function _command($args);
    
}