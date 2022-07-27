FROM node:14.16.0-buster AS NODE_BUILDER

WORKDIR /source
COPY ./public_html/ ./
RUN npm install

# Generate prod files /public/css/app.css && /public/js/app.js
RUN npm run prod


###################################
FROM php:7.4-apache

EXPOSE 8080
EXPOSE 4443
RUN mkdir -p /usr/share/man/man1

RUN apt-get update && apt-get install -y  \
    git \
    zip \
    curl \
    wget \
    sudo \
    unzip \
    libicu-dev \
    libbz2-dev \
    libpng-dev \
    libjpeg-dev \
    libmcrypt-dev \
    libreadline-dev \
    libfreetype6-dev \
    g++ \
    default-jdk

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN docker-php-ext-install \
    bz2 \
    intl \
    iconv \
    bcmath \
    opcache \
    calendar \
    pdo_mysql

COPY ./public_html/ /var/www/html/
COPY ./certs/ /etc/ssl/certs
COPY ./public_html/vhost.conf /etc/apache2/sites-available/guard-dashboard.conf
COPY ./public_html/ports.conf /etc/apache2/ports.conf

RUN echo "Servername dashboard.guard-project.eu" >> /etc/apache2/apache2.conf &&\
    a2enmod rewrite &&\
    a2dissite 000-default &&\
    a2ensite guard-dashboard &&\
    a2enmod ssl &&\
    service apache2 restart

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install

# BRING NODE COMPILED FILES /public/css/app.css && /public/js/*
COPY --from=NODE_BUILDER /source/public/css/app.css /var/www/html/public/css/app.css
COPY --from=NODE_BUILDER /source/public/css/smartdashboard.css /var/www/html/public/css/smartdashboard.css
COPY --from=NODE_BUILDER /source/public/css/custom.css /var/www/html/public/css/custom.css
COPY --from=NODE_BUILDER /source/public/js /var/www/html/public/js
COPY --from=NODE_BUILDER /source/public/images /var/www/html/public/images
COPY --from=NODE_BUILDER /source/public/plugins /var/www/html/public/plugins
COPY --from=NODE_BUILDER /source/public/limitless /var/www/html/public/limitless

RUN chown -R www-data:www-data /etc/ssl/
RUN chmod -R ug+rwx /var/www/html/storage /var/www/html/bootstrap/cache
RUN chown -R www-data:www-data /var/www/html

#create documentation
RUN php artisan larecipe:install