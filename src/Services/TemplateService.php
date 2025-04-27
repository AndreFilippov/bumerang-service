<?php

namespace App\Services;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TemplateService
{
    private Environment $twig;

    public function __construct()
    {
        $loader = new FilesystemLoader(__DIR__ . '/../../templates');
        $this->twig = new Environment($loader, [
            'cache' => __DIR__ . '/../../var/cache',
            'auto_reload' => true
        ]);
    }

    public function render(string $template, array $context = []): string
    {
        return $this->twig->render($template, $context);
    }
} 