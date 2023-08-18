FROM php:8.2-apache

# Set the working directory inside the container
WORKDIR /var/www/html

RUN pecl install redis-5.3.4 && \
    docker-php-ext-enable redis

# Install system dependencies
RUN apt-get update \
    && apt-get install -y \
        libicu-dev \
        libpq-dev \
        libzip-dev \
        unzip \
        git \
        cron \
        nano

# Install PHP extensions
RUN docker-php-ext-install \
    intl \
    zip \
    opcache


# Install symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

# Install Composer CLI
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
