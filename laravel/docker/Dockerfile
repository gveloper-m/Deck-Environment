FROM php:8.2-cli

RUN apt-get update && apt-get install -y git unzip zip libpq-dev curl && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install pdo pdo_pgsql || true

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
