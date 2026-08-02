# Usamos la imagen oficial de PHP con Apache
FROM php:8.2-apache

# Habilitamos mod_rewrite por si llegas a necesitar URLs amigables en el futuro
RUN a2enmod rewrite

# Copiamos el contenido de tu repositorio al directorio web de Apache
COPY . /var/www/html/

# Exponemos el puerto 80
EXPOSE 80
