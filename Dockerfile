FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    default-mysql-client \
    nginx \
    curl \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo pdo_mysql zip \
    && pecl install redis \
    && docker-php-ext-enable redis

RUN curl -sL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www
WORKDIR /var/www

RUN mkdir -p /var/www/storage /var/www/bootstrap/cache /var/www/public/build \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/public/build \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 777 /var/www/public/build

COPY docker/nginx/default.conf /etc/nginx/sites-available/default

EXPOSE 80 443

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

CMD bash -c "\
    php-fpm & \
    nginx -g 'daemon off;' & \
    php artisan queue:work --daemon --tries=3 & \
    while true; do php artisan schedule:run --no-interaction; sleep 60; done \
"