FROM php:7.4-cli

ARG ENVIRONMENT_NAME

COPY ./rootfilesystem/ /

RUN apt-get update && \
    apt-get install openssl -y && \
    apt-get install wget -y && \
    apt-get install htop -y && \
    apt-get install -y supervisor
RUN apt-get install -y libpq-dev libpng-dev

# Install php extensions
RUN docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd  && \
  docker-php-ext-configure mysqli --with-mysqli=mysqlnd && \
  docker-php-ext-install pdo pdo_mysql mysqli && \
  docker-php-ext-install json && \
  docker-php-ext-install gd && \
  docker-php-ext-install pcntl

# Install some other base extensions
RUN apt-get install -y libzip-dev zip \
 && docker-php-ext-install zip

# Redis stuff
RUN pecl install redis \
    && docker-php-ext-enable redis

# Install extra dependencies
RUN apt-get install curl -y \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]

WORKDIR "/var/www/html"
