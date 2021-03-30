FROM php:7.4-apache

MAINTAINER Andrey Kruglikov <andrey.kruglikov@ecomcharge.com>

RUN apt-get update \
  && pecl update-channels \
  && apt-get install -y --no-install-recommends \
    libzip-dev \
    libmagickwand-dev \
  && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
  && docker-php-ext-install gd \
    zip \
    pdo_mysql \
    mysqli \
  && pecl install \
    imagick \
  && docker-php-ext-enable imagick \
    #clear...
  && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /var/log/lastlog /var/log/faillog

RUN mkdir '/var/app' \
  &&apt-get update \
  && apt-get install -y --no-install-recommends \
    vim \
    unzip \
  && curl --request GET -sL \
        --url 'https://okay-cms.com/versions/OkayCMS_4.0.2.zip' \
        --output ./OkayCMS.zip \
  && unzip ./OkayCMS.zip -d ./ \
  && rm -rf ./OkayCMS.zip

VOLUME ["/var/www/html"]

COPY docker-entrypoint.sh /var/app

RUN chmod 755 /var/app/docker-entrypoint.sh

ENTRYPOINT ["/var/app/docker-entrypoint.sh"]

WORKDIR /var/www
