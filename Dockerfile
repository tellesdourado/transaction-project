FROM php:7.4-fpm

# installing laravel dependences
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    mariadb-client
# downloading composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# installing docker extensions to use mysql
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

#creating application directory and copying the project
WORKDIR /app
COPY . /app

# runnig project dependences installation and copying the env configuration
RUN composer install
COPY .env.example .env

# creating a file to run the migrations, tests and creating documentation before ran the server
RUN echo '\
php artisan migrate --seed && \
php artisan test && \
php artisan l5-swagger:generate && \
php artisan serve --host=0.0.0.0' >> /app/container_init.sh && \
chmod 755 /app/container_init.sh

# open the port to be access outside the container
EXPOSE 8000

# run the container
CMD /app/container_init.sh


