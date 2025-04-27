<?php

use PDO;

$pdo = new PDO('sqlite:database/database.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec('CREATE TABLE IF NOT EXISTS settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    key TEXT NOT NULL UNIQUE,
    value TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
)');

$stmt = $pdo->prepare('INSERT OR IGNORE INTO settings (key, value) VALUES (?, ?)');
$stmt->execute(['base_currency', 'EUR']); 