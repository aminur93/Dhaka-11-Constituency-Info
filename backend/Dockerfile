FROM php:8.3 AS php

# Install dependencies, PHP extensions, and clean up in a single layer
RUN apt-get update && apt-get install -y --no-install-recommends \
    unzip \
    zip \
    libzip-dev \
    libpq-dev \
    libcurl4-gnutls-dev \
    curl \
    libmemcached-dev \
    libz-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    libssl-dev \
    libwebp-dev \
    libxpm-dev \
    libmcrypt-dev \
    libonig-dev \
    && docker-php-ext-configure gd \
        --prefix=/usr \
        --with-jpeg \
        --with-webp \
        --with-xpm \
        --with-freetype \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        bcmath \
        zip \
        gd \
    && rm -rf /var/lib/apt/lists/*

# Update PHP configurations in the existing php.ini
RUN echo "upload_max_filesize = 500M" >> /usr/local/etc/php/php.ini \
    && echo "post_max_size = 500M" >> /usr/local/etc/php/php.ini

WORKDIR /var/www
COPY . .

# Install Composer in the same layer and install dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader

# Set permissions and create storage link (if necessary)
# RUN chown -R www-data:www-data storage \
#     && php artisan storage:link

# For staging and production environment
ENV PORT=8000
# Uncomment for production
# ENV PORT=80

RUN chmod a+x ./Docker/entrypoint.sh
ENTRYPOINT [ "sh", "./Docker/entrypoint.sh" ]
