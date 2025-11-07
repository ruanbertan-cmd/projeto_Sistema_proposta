FROM php:8.2-apache

# Instala extensões para MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Ativa o módulo de reescrita do Apache (Útil para URLs amigáveis)
RUN a2enmod rewrite

# Copia todos os arquivos do projeto para dentro do container
WORKDIR /var/www/html

# Copia apenas o arquivo de configuração do Apache (caso precise)
# COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Expõe a porta 80
EXPOSE 80