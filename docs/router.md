# Slimer Router

Slimer router based on [Slim router](http://www.slimframework.com/docs/v3/objects/router.html).

> Router config 

Slimer make up a configurable mechanism to generate the routes. [Router config](router-config)

> Inner magic function for the config loading

```PHP
        foreach ($this->config('routes') as $group => $routes) {
            //----support for route prefix by -  e.g.  api-cmd  the api is the route prefix
            if (\strpos($group,'-') > 0) {
	        $gs = \explode('-',$group);
                $group_name = \end($gs);
                $group = \str_replace('-','/',$group);
            }else{
                $group_name = $group;
            }
            $app->group($group, function () use ($group_name, $routes){
                $controller = ('/' === $group_name || !$group_name) ? 'index' : \trim($group_name, '/');
                
                foreach ($routes as $name => $route) {
                    $methods = isset($route['methods']) ? $route['methods'] : ['GET'];
                    $pattern = isset($route['pattern']) ? $route['pattern'] : '';
                    $callable = function (Request $request, Response $response, array $args = []) use ($controller, $route) {
                        //----support for Api type controller load
                        if (isset($route['namespace'])){
                            return $this['controller']($controller,$route['namespace'])->__invoke($request, $response, $args);
                        }else{
                            return $this['controller']($controller)->__invoke($request, $response, $args);
                        }
                    };
                    $this->map($methods, $pattern, $callable)->setName(('index' === $controller ? '' : $controller . '-').$name);
                }
            });
        }

```

> Slimer app run() to invoke Router generation

```PHP
 //Allow routing on application level via invokable class
        $router=$this->getContainer()['app_router'];
        $router($this);
```