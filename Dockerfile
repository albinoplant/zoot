FROM php:7.4-apache

RUN apt-get update && apt-get -y install \
    wget nano vim mc git \
    libzip-dev libxml2-dev \
    zip unzip

RUN docker-php-ext-install -j$(nproc) \
    pcntl \
    zip \
    pdo pdo_mysql \
    soap \
    sockets

RUN pecl install \
    apcu \
    xdebug

RUN docker-php-ext-enable \
    apcu \
    xdebug

RUN docker-php-ext-configure \
    pcntl --enable-pcntl

RUN echo "memory_limit = 1024M" > $PHP_INI_DIR/conf.d/php-memory-limits.ini \
    && echo "xdebug.mode = debug,coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host = host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && echo "alias ll='ls -alF'" >> ~/.bashrc

WORKDIR /etc/apache2/sites-available/
COPY apache/vhosts ./
RUN a2ensite *
RUN a2enmod rewrite

WORKDIR /var/www/html
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

EXPOSE 80
