FROM php:8.0-fpm

RUN apt-get dist-upgrade -y && apt-get update && apt-get install --fix-missing && apt-get upgrade -y && apt-get install -y \
    git \
    unzip \
    nano \
    curl \
    libzip-dev \
    procps \
    acl \
    parallel \
    bash

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=2.0.0-RC1
RUN export PATH=~/.composer/vendor/bin:$PATH

# Type docker-php-ext-install to see available extensions
# Install opcache
RUN docker-php-ext-configure opcache --enable-opcache \
    && docker-php-ext-install opcache

# Install Postgre PDO
RUN apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Update pecl repository
RUN pecl channel-update pecl.php.net
# Install APUc
RUN pecl install apcu
RUN echo "extension=apcu.so" > /usr/local/etc/php/conf.d/apcu.ini

# Set timezone
RUN rm /etc/localtime
RUN ln -s /usr/share/zoneinfo/Europe/Berlin /etc/localtime
RUN echo 'date.timezone = "Europe/Berlin"' > /usr/local/etc/php/php.ini

# ext intl
RUN apt-get install -y libicu-dev \
    && docker-php-ext-install intl

# ext soap
RUN apt-get install -y libxml2-dev  \
    && docker-php-ext-install soap

# install inkscape
RUN apt-get install -y inkscape \
    && ln -s /usr/bin/inkscape /usr/local/bin/inkscape

# math
RUN apt-get install -y libgmp-dev \
	&& ln -s /usr/include/x86_64-linux-gnu/gmp.h /usr/include/gmp.h \
	&& docker-php-ext-install \
		gmp \
		bcmath

# install xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.discover_client_host=On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN apt-get -y install gcc make autoconf libc-dev pkg-config && \
    apt-get -y install libssl-dev

RUN pecl install redis && docker-php-ext-enable redis

WORKDIR /var/www

EXPOSE 9000
