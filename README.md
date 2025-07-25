# Laravel Payme Integration Example

Этот проект демонстрирует **интеграцию платёжной системы Payme (Paycom)** в Laravel-приложение.  
Он может служить основой или примером для добавления онлайн-платежей в любые сервисы (продажа лицензий, событий и др.).

## 🚀 Возможности

- Приём платежей через систему **Payme (Paycom)**.
- Поддержка всех стадий транзакции:
    - Проверка возможности проведения (`CheckPerformTransaction`);
    - Создание (`CreateTransaction`);
    - Подтверждение выполнения (`PerformTransaction`);
    - Отмена транзакции (`CancelTransaction`);
    - Проверка состояния (`CheckTransaction`);
- Строгая структура через DTO, сервисы и репозитории.
- Поддержка различных сущностей: оплата событий, лицензий и т.д.

## ⚙️ Стек

- Laravel
- PHP 8+
- SQLite/MySQL
- REST API (приём JSON-запросов от Payme)

## 🧩 Структура интеграции

- **Контроллер**: `app/Http/Controllers/Payment/PaycomController.php`
- **Сервисы**: `app/Services/Payments/Paycom/`
- **DTO**: `app/DTO/Paycom/`
- **Модели**: `PaycomTransaction`, `Payment`, `LicensePayment`, `EventPayment`
- **Роут**: `routes/web.php` — POST-запрос на `/payme` (или другой URL, если настроено иначе)

## 🛠 Установка и запуск

```bash
git clone <repo_url>
cd <project_name>
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
