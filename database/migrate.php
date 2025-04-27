<?php

require_once __DIR__ . '/../vendor/autoload.php';

$migrations = [
    'create_settings_table.php',
    'create_visitors_table.php'
];

foreach ($migrations as $migration) {
    echo "Running migration: $migration\n";
    require_once __DIR__ . "/migrations/$migration";
    echo "Migration completed: $migration\n";
}

echo "All migrations completed successfully!\n"; 