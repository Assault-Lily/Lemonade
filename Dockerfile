FROM php:7.4-fpm

# install composer
RUN cd /usr/bin && curl -s http://getcomposer.org/installer | php && \
ln -s /usr/bin/composer.phar /usr/bin/composer && \
apt update \
&& apt install -y \
git \
zip \
unzip \
libzip-dev \
libmagickwand-dev \
vim \
nano \
&& pecl install imagick \
&& docker-php-ext-enable imagick \
&& docker-php-ext-install pdo_mysql zip

WORKDIR /var/www/html
