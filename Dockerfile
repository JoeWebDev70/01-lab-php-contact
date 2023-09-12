FROM php:8.1.23-apache
RUN docker-php-ext-install pdo_mysql
# replace module we need to activate in httpd.conf not present in this configuration
RUN a2enmod rewrite 
