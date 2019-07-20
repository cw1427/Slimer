# Slimer Project Structure

```
├── app                     # Folder, The Slimer Application folder
├── bin                     # Folder, The builtin php web server console file.
├── docs                    # Folder, The docs folder.
├── auth.json               # File, An optional composer local registry auth info
├── build.gradle            # File, The project gradle build file for docker
├── composer.json           # File, The php composer dependencies config file
├── composer.lock           # File, The composer lock file auto generated.
├── composer.phar           # File, The composer executor file
├── Data                    # Folder, The DB DDL file,for now support for mysql and Sqlite
├── docker                  # Folder, Keep some dockerization relevant files. 
├── docker-compose.yml      # File, A sample docker compose file to bring up Slimer.
├── Dockerfile              # File, The docker build file to make the docker image.
├── index.php               # File, The Slimer entrance file.
├── logs                    # Folder, The log output folder.  
├── README.md               # File, Default readme file.
├── session                 # Folder, Slimer file session store folder.
├── test.php                # File, test file
├── vendor                  # Folder, Slimer dependencies library folder.
└── version.ini             # File, Slimer version config file.
```

> Application structure under app/

```
├── Api                     # Folder, The Slimer RESTAPI Controller folder.
├── Cache                   # Folder, The Slimer cache auto generator folder.
├── Command                 # Folder, The Slimer basic commands folder.
├── Configs                 # Folder, The Slimer generic config folder including routers.
├── Controller              # Folder, The Slimer business controller mapping to the action request.
├── Exception               # Folder, The Slimer customized exceptions folder.
├── Helper                  # Folder, It was discarded, referred to Thinkphp, will be removed.
├── Main.php                # File, The main entrance file for Slimer, no need to modify.
├── Models                  # Folder, The model layer for Slimer, it will be extended by involoving ORM .
├── Provider                # Folder, the DI container provider file to register the service in container.
├── Static                  # Folder, the web static assets. CSS JS images
├── Templates               # Folder, the Slimer templates layer folder via twig
└── Util.php                # File, common helper functions file.
```