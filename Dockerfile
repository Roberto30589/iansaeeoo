# Usa una imagen base de PHP con FPM
FROM php:8.3-alpine

# Instala dependencias del sistema y PHP
RUN apk add --no-cache \
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
    nodejs \
    npm \
    bash \
    mysql-client \
    autoconf \
    g++ \
    make

# Instala extensiones de PHP necesarias para Laravel
RUN docker-php-ext-install pdo_mysql gd exif bcmath opcache zip intl soap pcntl dom mbstring

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /var/www

# Copia todo el código fuente
COPY . .

# Instala dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Compila frontend si corresponde (si usas Vite o Laravel Mix)
RUN npm install && npm run build

# Limpia cachés de Laravel (opcional pero recomendado)
RUN php artisan config:clear \
 && php artisan route:clear \
 && php artisan view:clear

# Laravel necesita APP_KEY (Cloud Run debería inyectarlo como variable de entorno)
# También puedes generarla aquí si estás en desarrollo:
# RUN php artisan key:generate

# Exponer el puerto 8080 (Cloud Run lo requiere)
EXPOSE 8080

# Servir Laravel con el servidor embebido de PHP
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
