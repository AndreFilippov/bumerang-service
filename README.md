# Bumerang Service

Сервис для отслеживания курсов валют с возможностью выбора базовой валюты и просмотра статистики посещений.

## Возможности

- Просмотр текущих курсов валют
- Выбор базовой валюты
- Автоматическое обновление курсов
- Логирование посещений
- Красивый и удобный интерфейс

## Требования

- PHP 8.1 или выше
- Composer
- Docker и Docker Compose (для запуска через Docker)
- SQLite3

## Установка и запуск

### Способ 1: Через Docker (рекомендуется)

1. Клонируйте репозиторий:
```bash
git clone https://github.com/AndreFilippov/bumerang-service.git
cd bumerang-service
```

2. Создайте файл .env на основе .env.example:
```bash
cp .env.example .env
```

3. Запустите контейнеры:
```bash
docker-compose up -d
```

4. Приложение будет доступно по адресу: http://localhost

## Структура проекта

```
bumerang-service/
├── config/             # Конфигурационные файлы
├── database/           # Файлы базы данных и миграции
├── docker/            # Конфигурация Docker
├── public/            # Публичные файлы
├── src/               # Исходный код
│   ├── Controllers/   # Контроллеры
│   └── Services/      # Сервисы
├── templates/         # Шаблоны Twig
├── vendor/            # Зависимости Composer
├── .env              # Переменные окружения
├── .env.example      # Пример переменных окружения
├── composer.json     # Зависимости Composer
├── Dockerfile        # Конфигурация Docker
└── README.md         # Документация
```

## API Endpoints

- `GET /` - Главная страница с курсами валют
- `POST /rates` - Получение курсов для выбранной валюты
- `POST /base` - Изменение базовой валюты

## Лицензия

MIT License 