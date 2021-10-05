FROM webdevops/php-nginx:7.4-alpine
LABEL maintainer="johan.van.der.heide@itea4.org"
LABEL org.opencontainers.image.source="https://github.com/eureka-clusters/portal-backend"

ENV TZ=Europe/Amsterdam
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

WORKDIR /var/www
COPY . .

#Do a fresh reinstall of the composer packages
#RUN rm -rf /var/www/vendor/*
RUN php composer.phar update --prefer-dist --no-interaction --no-dev

ENV WEB_DOCUMENT_ROOT="/var/www/public"
ENV PHP_DISMOD=ioncube,imagick,memcached,apcu,amqp
ENV PHP_DATE_TIMEZONE='Europe/Amsterdam'

ENV SSH_PORT 2222
EXPOSE 2222