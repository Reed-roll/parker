FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
# Ignore intl platform requirement during composer install, it will be installed in the final image
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-req=ext-intl

FROM php:8.5-apache
RUN apt-get update && apt-get install -y libsqlite3-dev zip unzip libicu-dev --no-install-recommends && rm -rf /var/lib/apt/lists/* 
RUN docker-php-ext-install pdo_sqlite intl
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