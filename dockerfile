FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction

FROM php:8.2-apache
RUN apt-get update && apt-get install -y libsqlite3-dev zip unzip && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install pdo_sqlite
RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's/DocumentRoot .*/DocumentRoot ${APACHE_DOCUMENT_ROOT}/g' /etc/apache2/apache2.conf

WORKDIR /var/www/html
COPY --from=vendor /app/vendor ./vendor
COPY . .
RUN chown -R www-data:www-data writable && chmod -R 775 writable

EXPOSE 80
CMD ["apache2-foreground"]