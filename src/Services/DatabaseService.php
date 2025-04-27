<?php

namespace App\Services;

class DatabaseService
{
    private \SQLite3 $db;

    public function __construct()
    {
        $dbPath = $_ENV['DB_PATH'] ?? __DIR__ . '/../../database/currency.db';
        $this->db = new \SQLite3($dbPath);
        $this->initializeDatabase();
    }

    private function initializeDatabase(): void
    {
        $this->db->exec('
            CREATE TABLE IF NOT EXISTS settings (
                key TEXT PRIMARY KEY,
                value TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ');

        $stmt = $this->db->prepare('SELECT COUNT(*) as count FROM settings WHERE key = ?');
        $stmt->bindValue(1, 'base_currency', SQLITE3_TEXT);
        $result = $stmt->execute();
        $count = $result->fetchArray(SQLITE3_ASSOC)['count'];

        if ($count == 0) {
            $stmt = $this->db->prepare('INSERT INTO settings (key, value) VALUES (?, ?)');
            $stmt->bindValue(1, 'base_currency', SQLITE3_TEXT);
            $stmt->bindValue(2, $_ENV['BASE_CURRENCY'] ?? 'EUR', SQLITE3_TEXT);
            $stmt->execute();
        }
    }

    public function getConnection(): \SQLite3
    {
        return $this->db;
    }

    public function runMigrations(): void
    {
        $migrations = [
            'create_visitors_table' => 'CREATE TABLE IF NOT EXISTS visitors (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                ip_address TEXT NOT NULL,
                user_agent TEXT NOT NULL,
                visited_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )'
        ];

        foreach ($migrations as $name => $sql) {
            $this->db->exec($sql);
        }
    }
} 