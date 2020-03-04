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
   			coreutils \
    		bash \
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
