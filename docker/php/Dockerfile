# Usa una imagen base de PHP con FPM para Alpine Linux
FROM php:8.3-fpm-alpine

# Argumento para el ID de usuario (para consistencia con el entorno local si es necesario)
ARG UID=1000
RUN adduser -D -u ${UID} laravel

# Instala dependencias del sistema necesarias para PHP y Laravel
RUN apk add --no-cache \
    nginx \              # Nginx se instalará aquí porque se ejecutará en el mismo contenedor
    supervisor \         # Supervisor para gestionar PHP-FPM y Nginx
    mysql-client \       # Cliente MySQL para interactuar con la DB
    git \
    unzip \
    libzip-dev \         # Para la extensión zip de PHP
    libpng-dev \         # Para la extensión gd de PHP
    jpeg-dev \           # Para la extensión gd de PHP
    libwebp-dev \        # Para la extensión gd de PHP
    freetype-dev \       # Para la extensión gd de PHP
    icu-dev \            # Para la extensión intl de PHP
    libpq \              # Si usas PostgreSQL, si no, puedes quitarlo
    libxml2-dev \        # Para la extensión dom de PHP
    zip \
    # ------ ¡Añade o verifica que estas estén aquí! ------
    oniguruma-dev \  # Para mbstring
    libgcc \         # Puede ser necesario para varias extensiones
    libstdc++        # Puede ser necesario para varias extensiones
    # ----------------------------------------------------


# Instala extensiones de PHP necesarias para Laravel
RUN docker-php-ext-install -j$(nproc) \
    pdo_mysql \          # Conexión a MySQL
    gd \                 # Procesamiento de imágenes
    exif \               # Procesamiento de metadatos de imágenes
    bcmath \             # Operaciones matemáticas de precisión
    opcache \            # Cache de bytecode para mejorar el rendimiento
    zip \                # Manejo de archivos zip
    intl \               # Internacionalización
    soap \               # Si usas SOAP
    pcntl \              # Para procesos en segundo plano/artisan queue (opcional)
    dom \                # Procesamiento de XML/HTML
    mbstring \           # Cadenas de caracteres multibyte
    fileinfo             # Detección de tipos de archivo MIME

# Instala Composer globalmente
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establece el directorio de trabajo dentro del contenedor
WORKDIR /var/www

# Copia los archivos de la aplicación al directorio de trabajo
# Primero copia composer.json y package.json para aprovechar el cache de Docker
COPY composer.json composer.lock ./
COPY package.json package-lock.json ./

# Instala las dependencias de Composer (solo producción, sin dev)
RUN composer install --no-dev --optimize-autoloader --no-scripts --prefer-dist

# Instala las dependencias de Node.js y compila los assets de Vue
# Asegúrate de que npm esté disponible, lo que se logra con Node.js en una imagen base.
# Si tu proyecto Vue está en un frontend separado, este paso no es necesario.
# Si usas Vite o Mix, esto generará los assets en public/build o public/js/css
COPY .npmrc .babelrc webpack.mix.js vite.config.js ./
COPY resources/ resources/
RUN npm install && npm run build

# Copia el resto de los archivos de la aplicación
COPY . .

# Elimina archivos innecesarios de la imagen final (depende de tu .dockerignore)
# RUN rm -rf .git .dockerignore Dockerfile docker-compose.yml .editorconfig .github

# Configura los permisos para Laravel
RUN chown -R laravel:laravel /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache \
    && find /var/www -type f -exec chmod 664 {} + \
    && find /var/www -type d -exec chmod 775 {} +

# Configura Nginx
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Configura PHP-FPM
COPY docker/php/php.ini /etc/php8/conf.d/custom.ini
# Puedes copiar un archivo www.conf si necesitas ajustes específicos para FPM,
# pero la configuración por defecto de Alpine suele ser suficiente si Nginx se conecta a 9000
# RUN sed -i 's/listen = 127.0.0.1:9000/listen = 9000/' /etc/php8/php-fpm.d/www.conf


# Configura Supervisor para iniciar Nginx y PHP-FPM
COPY docker/supervisor/supervisord.conf /etc/supervisord.conf

# Expone el puerto 8080 (Cloud Run requiere que la aplicación escuche en este puerto)
EXPOSE 8080

# Comando para iniciar Supervisor cuando el contenedor arranca
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]