# Dockerfile per PHP 8.0.28 + Apache
FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive
# -----------------------------
# Repository Sury (for PHP 8.0)
# -----------------------------
RUN apt-get update && apt-get install -y \
    software-properties-common ca-certificates lsb-release curl wget git && \
    add-apt-repository ppa:ondrej/php -y && \
    apt-get update

# -----------------------------
# Install Apache + PHP 8.0.28
# -----------------------------
RUN apt-get install -y \
    apache2 \
    libapache2-mod-php8.1 \
    php8.1 \
    php8.1-cli \
    php8.1-common \
    php8.1-mbstring \
    php8.1-xml \
    php8.1-zip \
    php8.1-curl \
    php8.1-gmp \
    php8.1-sqlite3 \
    php8.1-dom \
    php8.1-xdebug \
    php8.1-mysql


# -----------------------------
# Xdebug configuration
# -----------------------------
RUN echo "zend_extension=xdebug.so" >> /etc/php/8.1/apache2/conf.d/20-xdebug.ini && \
    echo "xdebug.mode=debug" >> /etc/php/8.1/apache2/conf.d/20-xdebug.ini && \
    echo "xdebug.start_with_request=yes" >> /etc/php/8.1/apache2/conf.d/20-xdebug.ini && \
    echo "xdebug.client_host=$XDEBUG_CLIENT_HOST" >> /etc/php/8.1/apache2/conf.d/20-xdebug.ini && \
    echo "xdebug.client_port=9003" >> /etc/php/8.1/apache2/conf.d/20-xdebug.ini && \
    echo "xdebug.log=/tmp/xdebug.log" >> /etc/php/8.1/apache2/conf.d/20-xdebug.ini

# -----------------------------
# Apache configuration
# -----------------------------
RUN a2enmod php8.1
RUN a2enmod rewrite
RUN a2enmod ssl

RUN sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Dev SSL self-signed (facoltativo)
RUN mkdir -p /etc/apache2/ssl && \
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/apache2/ssl/apache.key \
    -out /etc/apache2/ssl/apache.crt \
    -subj "/C=IT/ST=Italy/L=Local/O=Local/OU=Dev/CN=localhost"


# Abilita display errors per sviluppo
RUN sed -i "s/display_errors = .*/display_errors = On/" /etc/php/8.1/apache2/php.ini && \
    sed -i "s/error_reporting = .*/error_reporting = E_ALL/" /etc/php/8.1/apache2/php.ini

# -----------------------------
# Setup web root
# -----------------------------
WORKDIR /var/www/html

# -----------------------------
# Install Composer
# -----------------------------
RUN curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php && \
    php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer

# -----------------------------
# Permissions
# -----------------------------
RUN chown -R www-data:www-data /var/www && \
    chmod -R 775 /var/www

EXPOSE 80
EXPOSE 443
EXPOSE 9003
# -----------------------------
# Start Apache all’avvio
# -----------------------------
CMD ["apachectl", "-D", "FOREGROUND"]
