FROM php:7.3-fpm-alpine
  
RUN apk add imagemagick imagemagick-dev autoconf g++ imagemagick-dev libtool make pcre-dev && \
    pecl install imagick && docker-php-ext-enable imagick && \
    apk del imagemagick-dev autoconf g++ imagemagick-dev libtool make pcre-dev

WORKDIR /app
