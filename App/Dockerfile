# Utilise l'image officielle de Composer comme base
FROM composer:latest

# Copie le contenu de l'application dans le conteneur
COPY . /app

# Définit le répertoire de travail
WORKDIR /app

# Installer les dépendances nécessaires et l'extension PHP mongodb
RUN apk update && apk add --no-cache curl-dev openssl-dev pkgconfig autoconf g++ make && \
    pecl install mongodb && \
    docker-php-ext-enable mongodb

RUN docker-php-ext-install pcntl



# Installe les dépendances PHP
RUN composer install --no-interaction --prefer-dist --optimize-autoloader



# Expose le port sur lequel l'application va tourner (à ajuster si nécessaire)
EXPOSE 3000



