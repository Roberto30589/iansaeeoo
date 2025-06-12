FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    nginx \
    bash \
    nodejs \
    npm \
    git \
    curl \
    libzip-dev \
    zip \
    unzip \
    mysql-client \
    oniguruma-dev \
    autoconf \
    g++ \
    make \
    && docker-php-ext-install pdo pdo_mysql mbstring zip

RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

WORKDIR /app
COPY . /app

RUN composer install --no-dev --optimize-autoloader

RUN npm install && npm run build

COPY docker/nginx.conf /etc/nginx/nginx.conf
RUN mkdir -p /run/nginx

RUN chown -R www-data:www-data /app

# Limpiar cachés (solo si el archivo artisan existe)
RUN if [ -f artisan ]; then \
    php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear ; \
    fi

EXPOSE 8080
CMD ["sh", "/app/docker/startup.sh"]