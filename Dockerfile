# Usa una imagen base de PHP con FPM
FROM php:8.3-fpm-alpine

# Instala dependencias del sistema
RUN apk add --no-cache \
    nginx \
    supervisor \
    mysql-client \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    jpeg-dev \
    libwebp-dev \
    freetype-dev \
    icu-dev \
    libpq \
    libxml2-dev \
    zip \
    oniguruma-dev \
    libgcc \
    libstdc++

# Instala extensiones de PHP
RUN docker-php-ext-install pdo_mysql gd exif bcmath opcache zip intl soap pcntl dom mbstring

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establece el directorio de trabajo
WORKDIR /var/www

# Copia la aplicación Laravel
COPY . .

# Instala las dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Compila los assets de Vue (si los sirves desde Laravel)
# Si estás sirviendo tu frontend Vue de forma independiente, omite esto
RUN npm install && npm run build # Asegúrate de que npm esté disponible en la imagen o añádelo

# Configura Nginx
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Configura PHP-FPM
COPY docker/php/php-fpm.conf /etc/php8/php-fpm.d/www.conf
COPY docker/php/php.ini /etc/php8/conf.d/custom.ini

# Expone el puerto que usará Nginx
EXPOSE 8080

# Comando para iniciar Nginx y PHP-FPM con Supervisor
COPY docker/supervisor/supervisord.conf /etc/supervisord.conf
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]