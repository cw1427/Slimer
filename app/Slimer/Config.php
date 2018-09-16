<?php
/**
 * Author: Shawn Chen
 * Desc: A config set loader Class loading config set by file
 */
namespace Slimer;

use Psr\Container\ContainerInterface;

class Config
{
    /**
     * PSR-11 Container.
     *
     * @var ContainerInterface
     */
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    /**
     * Retrieves a configuration value. You can use a dot notation
     * to access properties in group arrays. The first part of the key
     * specifies the configuration file from which options should be loaded from
     * <code>
     *     //Loads ['default']['user'] option
     *     //from database.php configuration file
     *     $config('database.default.user');
     * </code>.
     *
     * @param string $string  configuration key to retrieve
     * @param string $default default value to return if the key is not found
     *
     * @return mixed Configuration value
     */
    public function __invoke( $string, $default = null)
    {
        $keys = \explode('.', $string);
        $group_name = \array_shift($keys);
        $group = $this->getGroup($group_name);
        if (!$keys) {
            return $group;
        }
        $total = \count($keys);
        foreach ($keys as $i => $key) {
            if (isset($group[$key])) {
                if ($i === $total - 1) {
                    return $group[$key];
                }
                $group = &$group[$key];
            }
        }
        
        return $default;
    }
    
    /**
     * Loads a group configuration file it has not been loaded before and
     * returns its options. If the group doesn't exist creates an empty one.
     *
     * @param string $name Name of the configuration group to load
     *
     * @return array Array of options for this group
     */
    protected function getGroup( $name )
    {
        if (!$this->container->has('config_'.$name)) {
            $this->loadGroup($name);
        }
        
        return $this->container->get('config_'.$name);
    }
    
    /**
     * Load group from file by group name.
     *
     * @param string $name
     */
    protected function loadGroup( $name )
    {
        $file = $this->container->get('config_dir').'/'.$name.'.php';
        $data = \is_file($file) ? include($file) : [];
        $this->container['config_'.$name] = $data;
    }
}
