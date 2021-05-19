FROM php:7.4-fpm
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    mariadb-client

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
WORKDIR /app
COPY . /app
RUN composer install
COPY .env.example .env

RUN echo '\
php artisan test && \
php artisan migrate --seed &&\
php artisan l5-swagger:generate && \
php artisan serve --host=0.0.0.0' >> /app/container_init.sh && \
chmod 755 /app/container_init.sh

EXPOSE 8000

CMD /app/container_init.sh


