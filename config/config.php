<?php

// Настройки базы данных
define('DB_PATH', __DIR__ . '/../database/database.sqlite');

// Создаем директорию для базы данных, если она не существует
if (!file_exists(dirname(DB_PATH))) {
    mkdir(dirname(DB_PATH), 0777, true);
}

// Создаем файл базы данных, если он не существует
if (!file_exists(DB_PATH)) {
    touch(DB_PATH);
} 