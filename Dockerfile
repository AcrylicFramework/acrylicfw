FROM php:8.3-apache

ENV APACHE_DOCUMENT_ROOT /var/www/acrylic/src
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN sed -ri -e 's/Listen 80/Listen 8888/g' /etc/apache2/ports.conf
RUN sed -ri -e 's/<VirtualHost \*:80>/<VirtualHost \*:8888>/g' /etc/apache2/sites-available/*.conf

RUN a2enmod rewrite headers

ENV TZ=UTC \
  LANG=en_US.UTF-8 \
  LANGUAGE=en_US:en \
  LC_ALL=en_US.UTF-8 \
  COMPOSER_ALLOW_SUPERUSER=1 \
  COMPOSER_HOME=/composer

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --version="2.6.6"
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer
RUN apt-get update

RUN apt-get -y install wget
RUN apt-get -y install --no-install-recommends locales git vim unzip libzip-dev libicu-dev libonig-dev
RUN apt-get clean
RUN rm -rf /var/lib/apt/lists/*
RUN locale-gen en_US.UTF-8
RUN localedef -f UTF-8 -i en_US en_US.UTF-8
RUN docker-php-ext-install intl zip mbstring
RUN composer config -g process-timeout 3600

WORKDIR /var/www/acrylic
EXPOSE 8888