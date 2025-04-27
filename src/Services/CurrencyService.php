<?php

namespace App\Services;

class CurrencyService
{
    private string $baseCurrency;
    private string $apiUrl;
    private DatabaseService $databaseService;
    private array $availableCurrencies;

    public function __construct(DatabaseService $databaseService)
    {
        $this->databaseService = $databaseService;
        $this->apiUrl = $_ENV['API_URL'] ?? 'https://api.frankfurter.dev/v1';
        
        $db = $this->databaseService->getConnection();
        $stmt = $db->prepare('SELECT value FROM settings WHERE key = ?');
        $stmt->bindValue(1, 'base_currency', SQLITE3_TEXT);
        $result = $stmt->execute();
        $baseCurrencyFromDb = $result->fetchArray(SQLITE3_ASSOC)['value'] ?? null;
        
        $this->baseCurrency = empty($baseCurrencyFromDb) ? ($_ENV['BASE_CURRENCY'] ?? 'EUR') : $baseCurrencyFromDb;
        $this->availableCurrencies = $this->getAvailableCurrencies();
    }

    public function getAvailableCurrencies(): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . '/currencies');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            throw new \Exception('Failed to fetch available currencies');
        }

        $data = json_decode($response, true);
        return array_keys($data);
    }

    public function isCurrencyValid(string $currency): bool
    {
        return in_array(strtoupper($currency), $this->availableCurrencies);
    }

    public function getRates(?string $currency = null): array
    {
        $targetCurrency = $currency ?? $this->baseCurrency;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . '/latest' . '?from=' . $targetCurrency);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            throw new \Exception('Failed to fetch currency rates');
        }

        $data = json_decode($response, true);
        return $data['rates'] ?? [];
    }

    public function setBaseCurrency(string $currency): void
    {
        if (!$this->isCurrencyValid($currency)) {
            throw new \InvalidArgumentException('Invalid currency');
        }

        $this->baseCurrency = strtoupper($currency);
        
        $db = $this->databaseService->getConnection();
        $stmt = $db->prepare('UPDATE settings SET value = ?, updated_at = CURRENT_TIMESTAMP WHERE key = ?');
        $stmt->bindValue(1, $this->baseCurrency, SQLITE3_TEXT);
        $stmt->bindValue(2, 'base_currency', SQLITE3_TEXT);
        $stmt->execute();
    }

    public function getBaseCurrency(): string
    {
        return $this->baseCurrency;
    }

    public function isValidCurrency(string $currency): bool
    {
        $rates = $this->getRates('EUR');
        return isset($rates[$currency]);
    }
} 