<?php
namespace App\Command;

use Slimer\Root;

/*
 * Author: Shawn Chen
 * Test simple command
 */

class SampleTask extends Root{
    
    /**
     * SampleTask command
     *
     * @param array $args
     * @return void
     */
    public function command($args)
    {
        // Access items in container
        $settings = $this->container->get('settings');
        
        // Throw if no arguments provided
        if (empty($args)) {
            throw new \RuntimeException("No arguments passed to command");
        }
        
        $firstArg = $args[0];
        $secondArg = $args[1];
        
        // Output the first argument
        return sprintf("%s - %d",$firstArg,$secondArg);
    }
}
