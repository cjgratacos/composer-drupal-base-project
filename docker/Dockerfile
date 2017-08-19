FROM php:7.1-apache

# ADD docker/apache_default /etc/apache2/sites-available/000-default.conf
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
RUN sed -i 's/DocumentRoot \/var\/www\/html/DocumentRoot \/var\/www\/drupal\/web/' /etc/apache2/sites-enabled/000-default.conf
RUN sed -i 's/DocumentRoot \/var\/www\/html/DocumentRoot \/var\/www\/drupal\/web/' /etc/apache2/sites-available/000-default.conf

RUN a2enmod headers
RUN a2enmod rewrite
RUN service apache2 restart

RUN apt-get update -y && apt-get upgrade -y

RUN apt-get install -y mysql-client

# install the PHP extensions we need
RUN apt-get install -y libpng12-dev libjpeg-dev libpq-dev  vim git openssh-server wget \
	&& rm -rf /var/lib/apt/lists/* \
	&& docker-php-ext-configure gd --with-png-dir=/usr --with-jpeg-dir=/usr \
	&& docker-php-ext-install gd mbstring opcache pdo pdo_mysql pdo_pgsql zip

COPY ./php/php.ini /etc/php5/apache2/conf.d/php.ini

# Clean /var/www
RUN rm -rf /var/www

# Create drupal home directory
RUN mkdir -p /var/www/drupal/web
RUN chown -R www-data:www-data /var/www/drupal/web

# Change Working Directorory
WORKDIR /var/www/drupal

