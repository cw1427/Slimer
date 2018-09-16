<?php
/**
 * Author: Shawn Chen
 * Desc: A deligator for the container
 */
namespace Slimer;

use Psr\Container\ContainerInterface;

class Root
{
    /**
     * PSR-11 Container.
     *
     * @var ContainerInterface
     */
    protected $container;
    
    /**
     * Storage for magic getter/setter.
     *
     * @var array
     */
    protected $data = [];
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    /**
     * Call method or getter/setter for property.
     *
     * @param  $method
     * @param array  $params
     *
     * @throws \Exception if method not implemented in class
     *
     * @return mixed Data from object property
     */
    public function __call($method = null, array $params = [])
    {
        $parts = \preg_split('/([A-Z][^A-Z]*)/', $method, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $type = \array_shift($parts);
        
        // Call current class method
        if (\method_exists($this, $method)) {
            return \call_user_func_array([$this, $method], $params);
        }
        
        // Call method from container
        if ($this->container->has($method)) {
            return \call_user_func_array($this->container[$method], $params);
        }
        
        // Call getter/setter
        if ('get' === $type || 'set' === $type) {
            $property = \strtolower(\implode('_', $parts));
            $params = (isset($params[0])) ? [$property, $params[0]] : [$property];
            
            return \call_user_func_array([$this, $type], $params);
        }
        
        throw new \Exception('Method "'.$method.'" not implemented.');
    }
    
    /**
     * Magic get from container.
     *
     * @param  $name
     *
     * @return mixed
     */
    public function __get( $name)
    {
        if ($this->container->has($name)) {
            return $this->container->get($name);
        }
        
        return null;
    }
    
    /**
     * Get property data, eg get('post_id').
     *
     * @param  $property
     * @param mixed  $default  Default value if property not exists
     *
     * @return mixed
     */
    public function get( $property, $default = null)
    {
        return isset($this->data[$property]) ? $this->data[$property] : $default;
    }
    
    /**
     * Set property data, eg set('post_id',1).
     *
     * @param  $property
     * @param mixed  $data
     *
     * @return $this
     */
    public function set( $property, $data = null)
    {
        $this->data[$property] = $data;
        
        return $this;
    }
    
    /**
     * Return all entity data as array.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * Set all data to entity.
     *
     * @param array $data
     *
     * @return Root
     */
    public function setData(array $data)
    {
        $this->data = \array_merge($this->data, $data);
        
        return $this;
    }
}
