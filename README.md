# Slimer
A light weight encapsulation of PHP Slim and AdminLTE template.

<H3>Please star it to encourage me.</h3>

Will enhance the RBAC control, mail util and so on feature in next version.

----------
# Based on
- Slim3: A smart and fast PHP micro framework https://www.slimframework.com/

- Twig: A flexible PHP web template engine. https://twig.symfony.com/

- Medoo: A light weight PHP ORM db library. https://medoo.in/api

- AdminLTE: A html web template


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

- Add SMTP mailer sender

- Add PHP ShellCommand to execute the command by exec or pro_open

- Add guzzle to bring the requests feature in Slimer.

- Add Back to Top feature and add bootstrap table, datetimerange feature.


----------

# Development guide

- 1. Configs

- 2. Routes

- 3. Controller

- 4. Views

- 5. Commands
  -- Built-in PHP CLI commands.  support for:  php index.php <command name> --foo --bar=baz --spam eggs  -a
  -- like:  php index.php Dbinit --help
  -- command example page
  ![command example page](https://github.com/cw1427/Slimer/blob/master/app/Static/img/cmd.png)





# Sample UI page:

----------

- login page
![Slimer login page](https://github.com/cw1427/Slimer/blob/master/app/Static/img/login.png)


- home page
![Slimer home page](https://github.com/cw1427/Slimer/blob/master/app/Static/img/admin.png)