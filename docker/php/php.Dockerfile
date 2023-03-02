FROM php:8.2.3-fpm
RUN sed -i 's/9000/9002/' /usr/local/etc/php-fpm.d/zz-docker.conf

WORKDIR /var/www/html

RUN docker-php-ext-install bcmath pdo pdo_mysql

RUN apt-get update && apt-get install -y cron vim supervisor

RUN apt-get -y install gcc make autoconf libc-dev pkg-config && \
    apt-get -y install libgpgme11-dev && \
    pecl install gnupg

RUN apt-get update && apt-get install -y gnupg2

RUN apt-get install -y \
        libzip-dev \
        zip \
  && docker-php-ext-install zip

RUN apt-get update && \
    apt-get install -y libxml2-dev
RUN docker-php-ext-install soap

RUN docker-php-ext-install opcache

RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y git

ADD . /var/www/html
RUN chown -R www-data:www-data /var/www

RUN mkdir /var/www/html/var/
RUN chmod 777 -R /var/www/html/var/

RUN pecl install -o -f redis \
&&  rm -rf /tmp/pear \
&&  docker-php-ext-enable redis

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
