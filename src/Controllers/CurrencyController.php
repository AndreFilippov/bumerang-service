<?php

namespace App\Controllers;

use App\Services\CurrencyService;
use App\Services\DatabaseService;
use App\Services\VisitorService;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class CurrencyController
{
    private CurrencyService $currencyService;
    private DatabaseService $databaseService;
    private VisitorService $visitorService;
    private Environment $twig;

    public function __construct()
    {
        $this->databaseService = new DatabaseService();
        $this->currencyService = new CurrencyService($this->databaseService);
        $this->visitorService = new VisitorService($this->databaseService);
        
        $loader = new FilesystemLoader(__DIR__ . '/../../templates');
        $this->twig = new Environment($loader);
    }

    public function index(): void
    {
        $this->visitorService->logVisit();
        
        $baseCurrency = $this->currencyService->getBaseCurrency();

        $currencies = $this->currencyService->getAvailableCurrencies();

        echo $this->twig->render('index.html.twig', [
            'baseCurrency' => $baseCurrency,
            'currencies' => $currencies
        ]);
    }

    public function getRates(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['currency'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Currency parameter is required']);
            return;
        }

        $currency = strtoupper(trim($data['currency']));

        try {
            $rates = $this->currencyService->getRates($currency);

            if (empty($rates)) {
                echo $this->twig->render('error.html.twig', [
                    'message' => 'Не удалось получить курсы валют. Пожалуйста, попробуйте позже.'
                ]);
                return;
            }

            echo $this->twig->render('rates_table.html.twig', [
                'rates' => $rates
            ]);
        } catch (\Exception $e) {
            echo $this->twig->render('error.html.twig', [
                'message' => 'Произошла ошибка при получении курсов валют. Пожалуйста, попробуйте позже.'
            ]);
        }
    }

    public function setBaseCurrency(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['currency'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Currency parameter is required']);
            return;
        }

        $currency = strtoupper(trim($data['currency']));
        
        try {
            $this->currencyService->setBaseCurrency($currency);

            echo json_encode(['success' => true]);
        } catch (\InvalidArgumentException $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal server error']);
        }
    }
} 