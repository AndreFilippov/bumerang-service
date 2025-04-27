<?php

use PDO;

$pdo = new PDO('sqlite:database/database.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec('CREATE TABLE IF NOT EXISTS visitors (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    ip_address TEXT NOT NULL,
    user_agent TEXT NOT NULL,
    visit_date DATETIME DEFAULT CURRENT_TIMESTAMP
)'); 