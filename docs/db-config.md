# DB config

Slimer for now official support for Mysql and Sqlite. But it would support for any DB type that [Medoo](https://medoo.in/doc) could.

But Slimer builtin module Admin which might use some incampibilaty written sql command in it. Welcome to raise issue, and PR is welcomed too. [Slimer](https://github.com/cw1427/Slimer/)

## db config file

Slimer has db.php config file under [Config dir](config?id=config-dir).

Please at least keep a item named 'default', which will be used in the built-in Admin module to access the db.

```PHP
<?php

/**
 * Author: Shawn Chen
 * Desc: The DB config
 */

return [
    'default' =>[
            'namespace' => '\App\Models\\',
            'database_type' => 'sqlite',
            'database_file' => APP_ROOT . DS . 'app.db',
            'database_name' => APP_ROOT . DS . 'app.db',
            'option' => [
                PDO::ATTR_CASE => PDO::CASE_NATURAL,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ],
        ],
    /*
    'default' =>[
        'namespace' => '\App\Models\\',
        'database_type' => 'mysql',
        'database_name' => 'slimer',
        'server' => '127.0.0.1',
        'username' => 'root',
        'password' => 'root',
        'charset' => 'utf8',
        'port' => 3306,
        'option' => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ],
    ],*/
    
];
```


## DB config register

Slimer use DI container provider to register DB into the container. 

```PHP
    public function register(Container $container)
    {
        $container['dbDefault'] = $this->setMedoo($container,'db.default');
        $container['entity'] = $this->setEntityLoader($container,'db.default');
    }
    
```

So in the Slimer Controller class, we can directly access this db instance to access DB. The key in the container **dbDefault** can be used directly as a Medoo instance to access db.

> For more DB operation please refer [Medoo](https://medoo.in) 

```PHP
$groupList = $this->dbDefault->select("perm_usergroup(a)",["[><]perm_group(b)"=>["a.GroupID"=>"ID"],"[><]user(c)"=>["a.UserID"=>"id"]],
                ["b.Name","b.Title","c.id"],["c.id"=>$uids]);
```

## DB source customizing

Most of time, we have to config more DB source. Pleaes try to add the config in db.php. 

For example. We want to add a new DB source named **repomgmt**. We can add below config in db.php

```PHP
    'repomgmt' => [
        'namespace' => '\App\Models\\',
        'database_type' => 'mysql',
        'database_name' => 'repomgmt',
        'server' => '127.0.0.1',
        'username' => 'root',
        'password' => file_exists('REPOMGMT_DB_PWD')? file_get_contents('REPOMGMT_DB_PWD') : 'root',
        'charset' => 'utf8',
        'port' => 3306,
        'option' => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ],
    ],
```

And registe it in the Application level provider file under app/Provider/Provider.php
```PHP
/**
 * Application Service Provider.
 */
class Provider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $container)
    {
        ......
        $container['dbRepo'] = $this->setMedoo($container,'db.repomgmt');
        .....
    }

    protected function setMedoo(Container $container,$bind)
    {
        return function () use ($container,$bind) {
            $config = $container['config']($bind);
            return new \Medoo\Medoo($config);
        };
    }
    .....
}

```

And then the **dbRepo** would be a new Medoo DB instance, could be used by this way: **$this->dbRepo->select(.....)**