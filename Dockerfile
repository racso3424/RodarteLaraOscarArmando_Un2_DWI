# Usar imagen oficial de PHP con Apache
FROM php:8.2-apache

# Instalar extensiones necesarias para MySQL PDO
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar el módulo mod_rewrite de Apache
RUN a2enmod rewrite

# Copiar el código del proyecto al directorio web de Apache
COPY . /var/www/html/

# Ajustar permisos para Apache
RUN chown -R www-data:www-data /var/www/html

# Exponer el puerto 80
EXPOSE 80
