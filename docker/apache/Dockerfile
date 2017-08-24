# from https://www.drupal.org/requirements/php#drupalversions
FROM php:7.1-apache

ARG COMPOSER_VERSION

RUN usermod -a -G www-data root && usermod -a -G root www-data

# Add docker to listen to localhost
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN rm -rf /etc/apache2/sites-enabled/ \
    && rm -rf /etc/apache2/sites-enabled/

# COPY Drupal Apache Configuration
COPY ./conf/drupal-default.conf /etc/apache2/sites-enabled/drupal-default.conf
COPY ./conf/drupal-default.conf /etc/apache2/sites-enabled/drupal-default.conf

# COPY PimpMyLog Apache Configuration
COPY ./conf/pml-default.conf /etc/apache2/sites-enabled/pml-default.conf
COPY ./conf/pml-default.conf /etc/apache2/sites-enabled/pml-default.conf

# COPY Ports Configuration for Apache
COPY ./conf/ports.conf /etc/apache2/ports.conf

RUN a2enmod headers
RUN a2enmod rewrite
RUN service apache2 restart

RUN apt-get update
# drush requires mysql client for anything more that "drush cr"
RUN apt-get install -y mysql-client wget curl tree zip tar git

# install the PHP extensions we need
RUN apt-get install -y libpng12-dev libjpeg-dev libpq-dev  vim git openssh-server wget \
	&& rm -rf /var/lib/apt/lists/* \
	&& docker-php-ext-configure gd --with-png-dir=/usr --with-jpeg-dir=/usr \
  # Install X Debug
	&& pecl install xdebug \
	&& echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
	&& docker-php-ext-install gd mbstring opcache pdo pdo_mysql pdo_pgsql zip \
	&& docker-php-ext-enable xdebug

COPY ./php/php.ini /etc/php5/apache2/conf.d/php.ini

# Clean /var/www and Recreate /var/www
RUN rm -rf /var/www/ && mkdir -p /var/www/

RUN cd /var/www/ \
    && wget -O - https://github.com/potsky/PimpMyLog/tarball/master | tar xzf - \
    && mv potsky-PimpMyLog-* pimpmylogs

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
		&& php composer-setup.php --version=$COMPOSER_VERSION\
		&& php -r "unlink('composer-setup.php');" \
		&& mv composer.phar /usr/local/bin/composer

# Create and change owner of /var/log/apache2
RUN rm -rf /var/log/apache2/ \
    && mkdir -p /var/log/apache2 \
    && touch /var/log/apache2/error.log \
    && touch /var/log/apache2/access.log \
    && touch /var/log/apache2/php_errors.log \
    && chmod 777 /var/log/apache2/ \
    && chown -R www-data:www-data /var/log/apache2/

# Create drupal home directory
RUN mkdir -p /var/www/drupal/web/sites/default/files  \
    && chown -R www-data:www-data /var/www/drupal/

# Change Working Directorory
WORKDIR /var/www/drupal

# Make www-data own the www folder
RUN chown -R www-data:www-data /var/www/

# Expose Ports: PimpMyLogs and XDebug
EXPOSE 8081 9000

# Print Information of Installed Product each time the server starts
RUN apache2 -v && php -v