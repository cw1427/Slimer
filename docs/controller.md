# Slimer Controller

Slimer Controller class are the action* class set which mapping to the router config. [Router-config](router-config)

## Controller loader

Slimer prvoide a controller loader service in the container to load the Controller class file accrodingly.

As default the controller namespace will be assigned via suit.namespaces.controller, buy we can customize it in the [Router-config](router-config)

> Convention

Slimer has a Controller naming convention with the router name. The Controller file name should be the routes config file name with capital.

For example the route file:  **admin.php** under app/Configs/routes related controller file should be **Admin.php** under app/Controller

```php
 protected function setControllerLoader(Container $container)
    {
        return $container->protect(function ($name,$clz=null) use ($container) {
            $parts = \explode('_', $name);
            $class = (isset($clz) && $clz !=null) ? $clz : $container['config']('suit.namespaces.controller', '\\App\\Controller\\');
            foreach ($parts as $part) {
                $class .= \ucfirst($part);
            }
            if (!$container->has('controller_'.$class)) {
                $container['controller_'.$class] = function ($container) use ($class) {
                    return new $class($container);
                };
            }
            
            return $container['controller_'.$class];
        });
    }
```

> Any snake named style would be translate to camel named style.

Slimer has a convention about the route name with '-' character, which is the spliter for the specific namespace Controller.

For example api-cmd.php route, it will be mapped to the app/Api/Cmd.php Controller if it has the namespace config in its route config.

```php
'premovement' => [
        'pattern' => '/premovement',
        'methods' => ['GET','POST'],
        'namespace' => '\\App\\Api\\'
    ],
```