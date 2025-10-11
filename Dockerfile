FROM php:8.2-apache

# Instala extensões para MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copia todos os arquivos do projeto para dentro do container
COPY . /var/www/html

# Porta que o Apache vai escutar
EXPOSE 80
