# Slimer
*A light weight encapsulation of PHP <a href="http://www.slimframework.com/docs/" target="blank"> Slim 3 </a> and [AdminLTE](https://adminlte.io/themes/AdminLTE/index2.html) template.*

*A WebMVC skeleton framework based on [Slimer-core](https://packagist.org/packages/slimer/slimer-core)*

*Built-in [Medoo](https://medoo.in) have multpi DB type support. So make sure the php_pdo_* DB driver had been installed.

1.X versions would based on PHP5.6, support for Sqlite3, Mysql, Pgsql adn so on which Meedo framework support for.

> Feature list

- Container
- Config suit
- RBAC
- RESTFUL API 
- Commands
- LDAP
- Message flash
- Mail template
- Guide
- Docker


> Version release history.


## v1.4.7
- Add dependency for slimer-core 1.1.2 to enable jump to redirect.

## v1.4.5
- Fix some bug for the menu quick search and the security management page adjustment.
- Add menu visible by permission feature.
- Add introjs support to make it configurable.
- Add webpage showLoading feature to add Ajax request page mask.
- Add Composer private repoistry dependencies config in composer.json


----------
# Based on
- Slim3: A smart and fast PHP micro framework https://www.slimframework.com/

- Twig: A flexible PHP web template engine. https://twig.symfony.com/

- Medoo: A light weight PHP ORM db library. https://medoo.in/api

- AdminLTE: A html web template

# Development guide 

## [guide doc](https://cw1427.github.io/Slimer)

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

- Introduction page
![Slimer intro page](https://github.com/cw1427/Slimer/blob/master/app/Static/img/intro.png)

- Menu quick search page
![Slimer menuq page](https://github.com/cw1427/Slimer/blob/master/app/Static/img/menuQuickSearch.png)