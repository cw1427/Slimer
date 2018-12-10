<?php
/**
 * Author: Shawn Chen
 * Desc: A very light weight ORM provider extension on medoo
 */
namespace Slimer\Orm;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class Provider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $container)
    {
        $container['dbDefault'] = $this->setMedoo($container,'db.default');
        $container['entity'] = $this->setEntityLoader($container,'db.default');
    }
    
    /**
     * Set Medoo into container.
     *
     * @param Container $container
     *
     * @return callable
     */
    protected function setMedoo(Container $container,$bind)
    {
        return function () use ($container,$bind) {
            $config = $container['config']($bind);
            return new \Medoo\Medoo($config);
        };
    }
    
    /**
     * Set entity() function into container.
     *
     * @param Container $container
     *
     * @return callable
     */
    protected function setEntityLoader(Container $container,$bind)
    {
        return $container->protect(function ($name) use ($container, $bind) {
            $parts = \explode('_', $name);
            $class = $container['config']($bind . '.namespace');
            foreach ($parts as $part) {
                $class .= \ucfirst($part);
            }
            if (!$container->has('entity_'.$class)) {
                $container['entity_'.$class] = $container->factory(function ($container) use ($class) {
                    return new $class($container);
                });
            }
            
            return $container['entity_'.$class];
        });
    }
}