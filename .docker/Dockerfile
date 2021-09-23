FROM php:5.6-fpm
RUN apt-get update \
    && apt-get install -y nginx-light git \
       libfreetype6-dev libjpeg62-turbo-dev libpng-dev libicu-dev
RUN docker-php-ext-configure gd \
    --with-gd \
    --with-jpeg-dir \
    --with-png-dir \
    --with-zlib-dir \
    --with-freetype-dir
RUN docker-php-ext-install intl gd pdo pdo_mysql \
    && docker-php-ext-enable intl gd pdo pdo_mysql
RUN mkdir /usr/local/src/wait-for-it \
    && cd /usr/local/src/wait-for-it \
    && git clone https://github.com/vishnubob/wait-for-it.git . \
    && cp ./wait-for-it.sh /usr/local/bin
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
COPY ./.docker/nginx.conf /etc/nginx/nginx.conf
COPY ./.docker/php.ini /usr/local/etc/php/php.ini
COPY ./.docker/entrypoint.sh /etc/entrypoint.sh
COPY . /app
RUN cd /app && chmod a+w runtime public/assets
EXPOSE 80 443
WORKDIR /app
ENTRYPOINT ["/etc/entrypoint.sh"]
