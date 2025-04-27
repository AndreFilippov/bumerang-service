FROM php:8.1-fpm

# Установка необходимых расширений PHP и утилит
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    nginx \
    supervisor \
    git \
    unzip \
    && docker-php-ext-install pdo_sqlite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка рабочей директории
WORKDIR /var/www

# Создание необходимых директорий
RUN mkdir -p /var/www/database \
    /var/www/storage/logs \
    /var/run/php-fpm \
    /var/log/php-fpm \
    /var/log/supervisor \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www

# Копирование только необходимых файлов для установки зависимостей
COPY composer.json composer.lock ./

# Установка зависимостей
RUN composer install --no-dev --optimize-autoloader

# Копирование файлов миграций
COPY database/migrations /var/www/database/migrations/
COPY database/migrate.php /var/www/database/

# Копирование остальных файлов проекта
COPY . .

# Копирование и настройка entrypoint скрипта
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Настройка NGINX
COPY docker/nginx/conf.d/app.conf /etc/nginx/conf.d/default.conf
RUN rm -f /etc/nginx/sites-enabled/default

# Настройка Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Открытие порта
EXPOSE 80

# Запуск через entrypoint скрипт
ENTRYPOINT ["/entrypoint.sh"]
