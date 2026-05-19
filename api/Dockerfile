FROM php:8.2-cli

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock* ./
RUN composer install --no-dev --no-interaction --optimize-autoloader 2>/dev/null || composer install --no-dev --no-interaction --optimize-autoloader

COPY . .

EXPOSE 9999

CMD ["php", "-S", "0.0.0.0:9999", "-t", "/var/www/html", "/var/www/html/app.php"]
