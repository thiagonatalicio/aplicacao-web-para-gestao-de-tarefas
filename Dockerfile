FROM php:7.4-apache

# Instala as dependências necessárias e habilita mysqli
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && docker-php-ext-enable mysqli

# Copia o código da aplicação para o container
COPY . /var/www/html/

# Permite mod_rewrite
RUN a2enmod rewrite
