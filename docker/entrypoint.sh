#!/bin/sh

set -e

# Создаем директорию для базы данных, если её нет
mkdir -p /var/www/database

# Устанавливаем правильные права
chown -R www-data:www-data /var/www
chmod -R 775 /var/www

# Проверка, существует ли база данных
if [ ! -f /var/www/database/database.sqlite ]; then
    echo "Database not found. Creating a new one."
    touch /var/www/database/database.sqlite
    chmod 666 /var/www/database/database.sqlite
else
    echo "Database file exists. Skipping creation."
fi

# Запускаем миграции
echo "Running database migrations..."
php /var/www/database/migrate.php

# Запуск Supervisor, который будет управлять процессами PHP-FPM и NGINX
echo "Starting Supervisor..."
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
