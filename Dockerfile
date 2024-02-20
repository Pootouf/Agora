FROM php:8.3-fpm

#Base dependencies
RUN apt-get -y update && apt-get -y install \
    git \
    zip \
    curl \
    unzip \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get -y autoremove

#Symfony CLI
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash \
    && apt install symfony-cli


#PHP Configuration
RUN pecl install xdebug \
    && docker-php-ext-install pdo_mysql opcache \
    && docker-php-ext-enable pdo_mysql opcache xdebug \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

#Composer installation
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


WORKDIR /var/www/agora


#Get the composer files
COPY composer.json composer.lock .env /var/www/agora/
COPY bin/console /var/www/agora/bin/

#Composer dependencies
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install


#Copy the project files
COPY . /var/www/agora


###> recipes ###
###< recipes ###

EXPOSE 9000