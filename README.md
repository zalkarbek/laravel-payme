# Laravel Payme (Paycom) Integration Example

Этот проект демонстрирует интеграцию платёжной системы **Payme (бывш. Paycom)** в Laravel-приложение.  
Может служить основой или примером для добавления онлайн-платежей в любые сервисы (продажа лицензий, событий и др.).

## 🚀 Возможности

- Полноценная поддержка JSON-RPC API Payme с обработкой всех жизненных циклов транзакции:
    - `CheckPerformTransaction` — проверка возможности проведения;
    - `CreateTransaction` — создание транзакции;
    - `PerformTransaction` — подтверждение выполнения;
    - `CancelTransaction` — отмена транзакции;
    - `CheckTransaction` — проверка состояния транзакции.
- Гибкая архитектура:
    - Каждый метод обрабатывается отдельным сервисом.
    - Валидация через динамические правила (интерфейс `RpcRulesInterface`).
- Строгая типизация и структура:
    - DTO (Data Transfer Objects)
    - Enums
    - Сервисы, репозитории и контрактные интерфейсы.
- Поддержка различных сущностей оплаты: события, лицензии и другие.

## ⚙️ Технологии

- Laravel
- PHP 8+
- SQLite / MySQL
- REST API (приём JSON-запросов от Payme)

## 📂 Структура проекта

```text
app/
├── DTO/Paycom/                   # DTO для входящих данных Payme
├── Services/Payments/Paycom/     # Бизнес-логика для каждого RPC метода
├── Http/Controllers/             # Контроллер для приёма запросов (PaycomController.php)
├── Http/Requests/Paycom/         # FormRequest с динамическими правилами валидации
│   └── RpcRules/                 # Rules-классы для каждого RPC метода
├── Models/                       # Модели: PaycomTransaction, Payment, LicensePayment, EventPayment
├── Repositories/                 # Слой доступа к данным
routes/
└── web.php                       # POST-запрос на /payme (или другой URL)
