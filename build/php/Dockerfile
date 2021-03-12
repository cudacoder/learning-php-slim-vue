FROM phpdockerio/php73-fpm:latest

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

# Install extentions
RUN apt-get update \
    && apt-get -y --no-install-recommends install  php7.3-mysql php-redis php7.3-sqlite3 php-xdebug \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*
