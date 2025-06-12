FROM php:8.3-fpm-alpine

# Instalar dependencias del sistema y PHP
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

# Crear carpeta de la app
WORKDIR /app

# Copiar archivos
COPY . .

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Construir assets
RUN npm install && npm run build

# Dar permisos
RUN chown -R www-data:www-data /app \
 && chmod -R 775 /app/storage /app/bootstrap/cache

# Copiar configuración de nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Exponer puerto
EXPOSE 8080

# Script de inicio
CMD ["sh", "/app/docker/startup.sh"]