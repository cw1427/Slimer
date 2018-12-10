<?php
/**
 * Author: Shawn Chen (cw1427)
 * Desc: The basic Slimer app to extend the Slim app to bring some magic methods.
 */

namespace Slimer;


class App extends \Slim\App
{
    /**
     * Override Slim constructor to add some magic.
     *
     * @param mixed $container
     */
    public function __construct($container = [])
    {
        /*
         * Try to find config_dir in passed config,
         * because it's required for config loading.
         * If not passed, use default `pwd`/config
         */
        if (!isset($container['config_dir'])) {
            $container['config_dir'] = \getcwd().'/Config';
        }
        
        // To register dependencies, we need container object, not array
        if (\is_array($container)) {
            $container = new \Slim\Container($container);
        }
        
        // Register system dependencies
        $container->register(new Provider());
        // If application has custom service provider, register it
        foreach ($container['config']('suit.providers', []) as $provider) {
            $container->register(new $provider());
        }
        
        $this->add($container['globalrequest_middleware']);
        foreach ($container['config']('suit.middlewares', []) as $middleware) {
            $this->add($container[$middleware]);
        }
        // Merge slim default settings with user defined
        $settings = \array_merge((array) $container['settings']->all(), $container['config']('suit.settings', []));
        unset($container['settings']);
        $container['settings'] = $settings;
	//----register the command
        $container['commands'] = $container['config']('suit.commands', []);
        
        // And, finally, run Slim constructor
        parent::__construct($container);
    }
    
    /**
     * {@inheritdoc}
     */
    public function run($silent = false)
    {
        //Allow routing on application level via invokable class
        $router=$this->getContainer()['app_router'];
        $router($this);
        
        return parent::run($silent);
    }
}