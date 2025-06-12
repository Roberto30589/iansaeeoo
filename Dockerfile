FROM php:8.3-fpm-alpine

RUN apk add --no-cache nginx wget nodejs npm curl git zip unzip libzip-dev libpng-dev oniguruma-dev

RUN mkdir -p /run/nginx

COPY docker/nginx.conf /etc/nginx/nginx.conf

RUN mkdir -p /app
COPY . /app
COPY ./src /app

RUN sh -c "wget http://getcomposer.org/composer.phar && chmod a+x composer.phar && mv composer.phar /usr/local/bin/composer"
RUN cd /app && \
    /usr/local/bin/composer install --no-dev

RUN chown -R www-data:www-data /app

RUN php artisan config:clear \
 && php artisan route:clear \
 && php artisan view:clear

RUN npm install && npm run build

CMD sh /app/docker/startup.sh