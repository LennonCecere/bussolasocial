# Base image com PHP 8.2
FROM php:8.2

# Atualiza pacotes e instala dependências do sistema
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    zip \
    wget \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libxml2-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    zip \
    xml \
    gd

# Configura o diretório como seguro para o Git
RUN git config --global --add safe.directory /var/www/html

# Instala o Composer globalmente
RUN wget https://getcomposer.org/installer -O composer-setup.php \
    && php composer-setup.php --install-dir=/usr/bin --filename=composer \
    && composer --version

# Define o diretório de trabalho dentro do container
WORKDIR /var/www/html

# Copia o código da aplicação para o container
COPY . /var/www/html

# Atualiza as dependências do Composer
RUN composer update --no-interaction --optimize-autoloader

# Expõe a porta 80 (caso necessário para um servidor como Laravel)
EXPOSE 80

# Comando padrão ao rodar o container
CMD ["php", "-S", "0.0.0.0:80", "-t", "public"]