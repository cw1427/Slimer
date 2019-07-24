# Slimer Configure in Container

## Config dir

Slimer App class allow customize the config_dir path when in the initialization

**app/Configs** is the default config_dir path when initialize Slimer

```PHP
//----start engine
$app = new \Slimer\App(['config_dir'=>APP_PATH . DS . 'Configs']);
$app->run();

```

## Config convention

- Slimer has a config group convention, every <name>.php under [Config dir](config?id=config-dir) is a Config group in the DI Container.
- Every config file must has return an PHP Array ([key=>value,...]) as the config items.

```PHP
// below is an example config content in  session.php under config dir.
<?php
/**
 * Author: Shawn Chen
 * Desc: 
 */

return  [
    'name' => 'PHPSESSION',
    'lifetime' => '24 hour',
    'autorefresh' => false,
    'path' => '/',
    'domain' => null,
    'secure' => false,
    //'httponly' => true,
    //'handler' => null,
    'ini_settings' => [
        'session.save_path' => APP_ROOT . DS . 'session',
    ]
];

```

## Container suit config

Slimer has a Config Util Class to handle all of the Configs under the Configs folder. It will load the configs by group(the config file name as the group name).

```PHP
        $container['suit_config'] = function ($c) {
            return new Config($c);
        };
        $container['config'] = $container->protect(function ($string, $default = null) use ($container) {
            return $container['suit_config']->__invoke($string, $default);
        });
```

> Access config item in Slimer

Slimer has config key in container, so we can access config
```PHP
// Recommand use container['config'] accessor
$this->container['config']('groupname.configkey');
// Alternatively, we could directly use config() function to access the config.
$this->config("gerrit.name}"); // will access gerrit.php config file name key in gerrit.php
```

## Config Class design

Slimer Config class is an util class for config items loading and grouping.

> __invoke(string,default=null) a PHP magic functino to provide config retrieve.

```PHP
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

```