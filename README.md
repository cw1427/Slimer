# Slimer
A light weight encapsulation of PHP Slim and AdminLTE template.

<H3>Please star it to encourage me.</h3>

A WebMVC skeleton framework based on https://packagist.org/packages/slimer/slimer-core

Full feature MVC, RBAC, RESTFUL API, Backend commands.

----------
# Based on
- Slim3: A smart and fast PHP micro framework https://www.slimframework.com/

- Twig: A flexible PHP web template engine. https://twig.symfony.com/

- Medoo: A light weight PHP ORM db library. https://medoo.in/api

- AdminLTE: A html web template

----------

# Common feature on V1.4.1
- Refactor slimer project, move some core codes into slimer-core as dependency(which will release to packgist website)
- Split slimer core folder out of this project, and release it to: https://packagist.org/packages/slimer/slimer-core
- Change the composer dependency from slimer/slimer-core on packgist:  https://packagist.org/packages/slimer/slimer-core

# Common feature on V1.3.0
- Add RBAC control backend CLI command and UI management pages.

# Common feature on V1.2.2
- Add Http basic authen feature.

# Common feature on V1.2

- Dockerization the Slimer
- Setup Slimer running in php-fpm mode
- Add nginx fastcgi support
- Add gradle build support

----------

# Common feature on V1.1
- Add SMTP mailer sender
- Add PHP ShellCommand to execute the command by exec or pro_open
- Add guzzle to bring the requests feature in Slimer.
- Add Back to Top feature and add bootstrap table, datetimerange feature.

----------

# Common feature on V1.0
- Basic login authentication based on DB and LDAP.

- Basic MVC structure like Java Structs2. (request mapping to the Controller action function).

- Dependency Intergection container extends by provider. 

- Extendable configuration by module files.

- Light weight Slim-Flash wrapper to bring easily alert feature in page.

- Implement adminLTE template in Slimer.

- PHP Session control based on interface.

- Log management.

- Flexable web page menu config.

----------

# Development guide

- -1. Prepare
  -- Modify the db connection config in: app/Configs/db.php. Slimer support for all of the dbs which: https://medoo.in/ support for.
  -- But for the RBAC, for now I used php-rbac 3rd part library which could only support for mysql or sqlite. Will refactor and implement by medoo.  
  
- 0. Initialization
  -- When we want to start play Slimer, just need to run "php composer.phar install","php composer.phar update" in the project root path to install all of the dependencies.
  -- Run "php index.php cmd -l" to check all of the built-in commands.
  -- And then run the "php index.php dbinit --sync --dbEngine dbDefault to init the DB table and the data.
  -- If you need RBAC permission controll, just run the RBAC related command to do the initialization.
     Because I used php-rbac library as the bottom library which for now only support for mysql|sqlite3. 
     I will refactor it as a part of slimer.
  	 ----Run the "php index.php rbacinit --dbEngine dbDefault" to init the DB table and the data.
  	 ----Run "php index.php rbacinit --dbEngine dbDefault" to init the rbac tables.
  	 ----Run "php index.php rbacreset --force" to init the basic rbac metadata.
  -- Make sure you have made the DB tables and init data DDL sql in file under Data folder.  e.g  users.sql

- 1. Configs

- 2. Routes

- 3. Controller

- 4. Views

- 5. Commands
  -- Built-in PHP CLI commands.  support for:  php index.php <command name> --foo --bar=baz --spam eggs  -a
  -- like:  php index.php Dbinit --help
  -- command example page
  ![command example page](https://github.com/cw1427/Slimer/blob/master/app/Static/img/cmd.png)
  
- 6. Dockerization 
  -- Add Dockerfile to made the slimer to be dockerization and support for php-fpm with Nginx.
  
- 7. Add gradle support
  -- build the docker command:  gradle -Dregistry=<your local docker registry> slimerDocker <-Dpush=true>





# Sample UI page:

----------

- login page
![Slimer login page](https://github.com/cw1427/Slimer/blob/master/app/Static/img/login.png)


- home page
![Slimer home page](https://github.com/cw1427/Slimer/blob/master/app/Static/img/admin.png)