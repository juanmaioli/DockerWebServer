# Dockerfile para PHP 8.4 con Apache y Docker CLI
FROM php:8.4-apache

# Instalamos dependencias del sistema y extensiones de PHP
RUN apt-get update && apt-get install -y --no-install-recommends \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    ca-certificates \
    curl \
    arp-scan \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mysqli zip \
    && docker-php-ext-enable gd mysqli zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalamos el CLIENTE de Docker (Docker CLI) desde el binario estático
RUN curl -fsSL https://download.docker.com/linux/static/stable/x86_64/docker-27.3.1.tgz | tar -xzC /usr/local/bin --strip-components=1 docker/docker

# Habilitamos módulos de Apache
RUN a2enmod rewrite headers ssl && a2ensite default-ssl

# Creamos el grupo docker con el GID dinámico para que coincida con el host
# Si el grupo ya existe (ej: GID 100), lo usamos; si no, lo creamos.
ARG DOCKER_GID=126
RUN if getent group ${DOCKER_GID}; then \
        group_name=$(getent group ${DOCKER_GID} | cut -d: -f1); \
        usermod -aG $group_name www-data; \
    else \
        groupadd -g ${DOCKER_GID} docker_host && usermod -aG docker_host www-data; \
    fi

# Ajustamos permisos de la web
RUN chown -R www-data:www-data /var/www/html
