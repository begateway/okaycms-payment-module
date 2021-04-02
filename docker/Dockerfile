FROM php:7.4-apache

MAINTAINER Andrey Kruglikov <andrey.kruglikov@ecomcharge.com>

ARG version

RUN apt-get update \
  && pecl update-channels \
  && apt-get install -y --no-install-recommends \
    libzip-dev \
    libmagickwand-dev \
    vim \
    unzip \
  && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
  && docker-php-ext-install gd \
    zip \
    pdo_mysql \
    mysqli \
  && pecl install \
    imagick \
  && docker-php-ext-enable imagick \
  && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /var/log/lastlog /var/log/faillog


RUN a2enmod rewrite

RUN mkdir /var/app

ADD https://okay-cms.com/versions/OkayCMS_$version.zip /var/www/html

VOLUME ["/var/www/html"]

COPY docker-entrypoint.sh /var/app

RUN chmod 755 /var/app/docker-entrypoint.sh

ENTRYPOINT ["/var/app/docker-entrypoint.sh"]

WORKDIR /var/www/html
