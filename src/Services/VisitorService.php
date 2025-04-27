<?php

namespace App\Services;

class VisitorService
{
    private \SQLite3 $db;

    public function __construct(DatabaseService $databaseService)
    {
        $this->db = $databaseService->getConnection();
    }

    public function logVisit(): void
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

        $stmt = $this->db->prepare('INSERT INTO visitors (ip_address, user_agent) VALUES (:ip, :user_agent)');
        $stmt->bindValue(':ip', $ip, SQLITE3_TEXT);
        $stmt->bindValue(':user_agent', $userAgent, SQLITE3_TEXT);
        $stmt->execute();
    }
} 