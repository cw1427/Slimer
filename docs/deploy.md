# Slimer application deploy

Slimer app support for both Linux and Windows running env. As recommand, Slimer delivered as a Docker image witch Nginx and php-fpm mode. But Slimer also has Apache config file for refer.

## Apache config

Please refer the source code docker/apache/* config file

## Nginx

Please refer docker/nginx/slimer.conf

```nginx
server {
    listen         81;
    server_name    localhost;
    root           /slimer;
    index          index.php;

  location / {
     index  index.php index.html index.htm;
     if (-e $request_filename) {
      break;
  	}
      if (!-e $request_filename) {
       rewrite ^/(.*)$ /index.php/$1 last;
       break;
      }
  }
  
  location ~* ^.+.(jpg|jpeg|gif|css|png|js|ico|woff2|woff|ttf)$ {
      root /slimer_static;
	  access_log        off;
	  expires           30d;
  }
  
  location ~ ^(.+\.php)(.*) {
    fastcgi_index  index.php;
    fastcgi_split_path_info ^i(.+?\.php)(/.*)$;
    fastcgi_pass   127.0.0.1:9000;
    include         fastcgi_params;
    fastcgi_param   PATH_INFO          $fastcgi_path_info;
    fastcgi_param   SCRIPT_FILENAME    $document_root$fastcgi_script_name;
    fastcgi_param   PATH_TRANSLATED    $document_root$fastcgi_path_info;
    fastcgi_param   SCRIPT_NAME        $fastcgi_script_name;
    fastcgi_read_timeout 600s;
  }

}

```

## php-fpm config

Please refer source code docker/pool.d/slimer.conf


## docker

Slimer use php5.6.38 as its basic docker images, and installed necessary dependencies accordingly

```dockerfile
FROM php:5.6.38-fpm-alpine

COPY ./docker/gosu /usr/local/bin/    
# Install build deps, then run `pip install`, then remove unneeded build deps all in a single step. Correct the path to your production requirements file, if needed.
ENV APP_ENV dev
ENV slimer_USER slimer
ENV GOSU_USER 0:0
ENV GOSU_CHOWN /slimer

RUN set -ex \
    && adduser -D -S -h /slimer -s /bin/sh -g 9999 ${slimer_USER} \
    && apk add --no-cache --virtual .build-deps \
            libldap\
            gcc \
            make \
            openldap-dev \
            zlib-dev \
            libc-dev \
            gd-dev\
            musl-dev \
            linux-headers \
            pcre-dev \
            mariadb-dev \
            mariadb-client \
            libev \ 
            freetype \
            freetype-dev \
            libjpeg \
            libjpeg-turbo \
            libjpeg-turbo-dev	\
            libpng \
            libpng-dev \
            openssh \ 
    && chmod +x /usr/local/bin/gosu 
    
#----install the php extension
RUN set -ex \
	&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
	&& docker-php-ext-install -j$(nproc) gd \
	&& docker-php-ext-install -j$(nproc) ldap \
	&& docker-php-ext-install -j$(nproc) pdo \
	&& docker-php-ext-install -j$(nproc) mysql \
	&& docker-php-ext-install -j$(nproc) mysqli \
	&& docker-php-ext-configure pdo_mysql --with-pdo-mysql=/usr/ \
	&& docker-php-ext-install -j$(nproc) pdo_mysql

# Copy in your requirements file
COPY ./docker/entrypoint.sh  /slimer/
COPY ./docker/php-fpm.conf  /slimer/
COPY ./docker/pool.d  /slimer/pool.d
COPY ./docker/php.ini /usr/local/etc/php/php.ini
COPY . /slimer

RUN chmod +x /slimer/entrypoint.sh
WORKDIR /slimer
VOLUME /slimer/logs
VOLUME /slimer/session
# uWSGI will listen on this port
EXPOSE 9000
ENTRYPOINT ["sh","/slimer/entrypoint.sh"]
CMD [ "/usr/local/sbin/php-fpm","-F", "-y", "/slimer/php-fpm.conf" ]

```



## gradle

Slimer use gradle as the build and deployment tool to do the dockerization.

> gradle slimerDocker

Run this command gradle will help to build the current docker image according the dockerfile

```gradle
// Slimer build image and deliver
//apply plugin: 'application'
apply plugin: 'docker'
//Setup gradle-appengine plugin
buildscript {

    repositories {
        //maven {
        //    url "<your local maven registry nexus>"
        //}
        jcenter()
        mavenCentral()
    }
    dependencies {
        classpath 'se.transmode.gradle:gradle-docker:1.2'
    }

}

repositories {

   //maven {
   //     url "<your local maven registry nexus>"
   //}
   jcenter()
   mavenCentral()
}

dependencies {
}

group = "slimer"

docker{
    dockerBinary='docker'
}

task slimerDocker(type:Docker){
    dockerfile='Dockerfile'
    String cid=commitID()
    setEnvironment("slimer_COMMIT_ID", cid?cid:'NA')
    applicationName = 'slimer_dev'
    tagVersion = getVersion()
    if (System.properties['registry'] != null) {
        registry = System.properties['registry']
    }else{
        registry = '<your default private docker registry server>'
    }
    tag = "${registry}/${project.group}/${applicationName}"
    stageDir = project.rootDir
    if (System.properties['push'] == 'true' ){
        push = true
    }
}


def getVersion(){
    Properties version = new Properties()
    File pf = new File(rootProject.getRootDir().getAbsolutePath()+'/version.ini')
    pf.withInputStream {
       version.load(it)
    }
    print version.get('VERSION_BUILD')
    return String.format("%s.%s.%s",version.get('VERSION_MAJOR'),version.get('VERSION_MINOR'),version.get('VERSION_BUILD'))
}

def commitID() {
    def stdout = new ByteArrayOutputStream()
    exec {
        //commandLine 'git', 'show', '-s', '--pretty=oneline'
        commandLine 'git', 'rev-parse', '--short', 'HEAD'
        standardOutput = stdout
        ignoreExitValue = true
    }
    return stdout.toString().trim()
}

```


