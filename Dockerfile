FROM php:8.3-fpm

# Install system dependencies & PostgreSQL dev libraries
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions required for Laravel & PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql zip

# Install Redis extensions and enable it
RUN pecl install redis \
    && docker-php-ext-enable redis

# Set working directory
WORKDIR /var/www

# Copy existing application directory
COPY . /var/www

EXPOSE 9000
CMD ["php-fpm"]
