# dockerfile
FROM php:8.0-fpm

MAINTAINER 'Johan van der Heide <info@jield.nl>'

LABEL org.opencontainers.image.source="https://github.com/eureka-clusters/portal-backend"

# Copy composer.lock and composer.json
COPY composer.json* /var/www/

RUN echo 'memory_limit = 1G' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini;

# Set working directory
WORKDIR /var/www

# -----------------------

RUN apt-get update && \
apt-get install -y --force-yes --no-install-recommends \
	libmemcached-dev  \
	libfreetype6-dev \
	libxml2-dev \
	libjpeg62-turbo-dev \
	libpng-dev \
	zlib1g-dev \
	libzip-dev \
	libz-dev \
	libpq-dev  \
	libsqlite3-dev  \
	libicu-dev \
	g++ \
	git \
	zip \
	libmcrypt-dev \
	libvpx-dev \
	libjpeg-dev \
	libpng-dev \
	bzip2 \
	wget \
	libexpat1-dev \
	libbz2-dev \
	libgmp3-dev \
	libldap2-dev \
	unixodbc-dev \
	libsnmp-dev \
	libpcre3-dev \
	libtidy-dev \
	libaspell-dev \
	tar \
	less \
	nano \
	libcurl4-gnutls-dev \
	apt-utils \
	libxrender1 \
	unzip \
	libonig-dev \
	libldap2-dev \
	libxslt-dev \
	libwebp-dev \
	libc-client-dev \
	libkrb5-dev \
	libpspell-dev \
	librabbitmq-dev \
	librabbitmq4 \
&& phpModules=" \
            bcmath \
            bz2 \
            calendar \
            exif \
            gettext \
            gmp \
            intl \
            mysqli \
            opcache \
            pcntl \
            pdo_mysql \
            pdo_pgsql \
            pgsql \
            pspell \
            shmop \
            snmp \
            soap \
            sockets \
            sysvmsg \
            sysvsem \
            sysvshm \
            tidy \
            xsl \
            zip \
        " \
&& docker-php-ext-install -j$(nproc) $phpModules \
&& pecl install memcached-3.1.5 \
&& pecl install redis-5.3.2 \
&& pecl install igbinary-3.1.6 \
&& docker-php-ext-enable memcached redis igbinary

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy existing application directory contents
COPY ./ /var/www

RUN composer install --no-dev --prefer-dist --no-interaction

CMD ["php-fpm"]
