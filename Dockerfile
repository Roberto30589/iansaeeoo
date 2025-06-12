FROM php:8.3-fpm-alpine

# Instalar dependencias del sistema y extensiones de PHP
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

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

WORKDIR /app

# Copiar archivos de Composer y NPM para cacheo
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

COPY package.json package-lock.json ./
RUN npm install

# Copiar el resto del código después de instalar dependencias
COPY . .

# Compilar assets
RUN npm run build

# Configurar NGINX
COPY docker/nginx.conf /etc/nginx/nginx.conf
RUN mkdir -p /run/nginx

# Asignar permisos
RUN chown -R www-data:www-data /app

# Limpiar cachés de Laravel si existe artisan
RUN if [ -f artisan ]; then \
    php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear ; \
    fi

EXPOSE 8080
CMD ["sh", "/app/docker/startup.sh"]
