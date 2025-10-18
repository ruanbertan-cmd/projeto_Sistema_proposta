FROM php:8.2-apache

# Instala extensões para MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Ativa o módulo de reescrita do Apache (Útil para URLs amigáveis)
RUN a2enmod rewrite

# Copia todos os arquivos do projeto para dentro do container
WORKDIR /var/www/html

# Porta que o Apache vai escutar
EXPOSE 80
