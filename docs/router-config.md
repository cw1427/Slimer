# Router Config

[Slim Route](http://www.slimframework.com/docs/v3/objects/router.html)

## Routes entrance config file

Slimer setup a routes.php file under [Config dir](config?id=config-dir). And has initialization routes group setup under '/' key in routs.php file.

routs.php file is the Router config entrance config file, it take charge of all of the business routes loading, grouping by file.

> Load routes file 

routes.php file use below code to automatic load the routes under (Config dir)/routes/*.php and the file name is the routes group name.

```PHP
foreach (\glob(__DIR__.'/routes/*.php') as $item) {
    $group = \current(\explode('.', \basename($item)));
    $routes['/'.$group] = include $item;
}

return $routes;

```

> routes config 

Every route config file should return an PHP array to define the route info.

The **key** in the array is the route name, the value is an array which has "pattern","methods","perm","namespace" setup.

 - **pattern**  it is the Slim uri pattern
 - **methods**  it is the Slim route methods
 - **perm**     it is an array, setup this route allow access permission name.
 - **namespace** it is a string, it indicate this route mapping Controller namepsace.

```PHP
<?php

return [
    'form' => [
        'pattern' => '/form',
        'methods' => ['GET','POST'],
    ],
    'form_submit' => [
        'pattern' => '/formsubmit',
        'methods' => ['POST'],
    ],
    'table' => [
        'pattern' => '/table',
        'methods' => ['GET','POST'],
    ],
    'table_list' => [
        'pattern' => '/tablelist',
        'methods' => ['GET','POST'],
    ],
    'table_add' => [
        'pattern' => '/tableadd',
        'methods' => ['GET','POST'],
    ],
    'table_edit' => [
        'pattern' => '/tableedit',
        'methods' => ['GET','POST'],
    ],
    'table_delete' => [
        'pattern' => '/tabledelete',
        'methods' => ['DELETE'],
    ],

];
```

