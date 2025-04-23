# 🛠️ Fundslack – Fund Management System

A Laravel 12 backend system designed for managing investment funds, their aliases, fund managers, and related companies.

---

## 📌 Overview

This project provides a complete backend service to manage fund records and support data curation workflows, including duplicate detection via event-driven architecture.

It includes:

- Fully normalized relational data model
- RESTful API with filtering and CRUD support
- Duplicate fund detection using events & listeners
- Swagger (OpenAPI) documentation
- Docker support for local development

---

## 🧱 Tech Stack

- Laravel 12
- MySQL (via Docker)
- Docker & Docker Compose
- Swagger (l5-swagger)

---

## 🧰 Features

- Create, read, update and delete funds
- Relate funds with fund managers and companies
- Add aliases to funds
- Detect potential duplicates by name or alias per manager
- Log and handle duplicate fund warnings via Laravel events

---

## 🚀 Getting Started

### 1. Clone the repo

```bash
git clone https://github.com/cmprc/fundslack.git
cd fundslack
```

### 2. Start containers

```bash
docker-compose up -d
```

### 3. Install dependencies

```bash
composer install
```

### 4. Set up environment

```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` with:

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=canoe
DB_USERNAME=canoe
DB_PASSWORD=secret
```

### 5. Run migrations and seed

```bash
php artisan migrate --seed
```

---

## 📚 API Documentation

Swagger docs available at:

```
http://localhost:8000/api/documentation
```

Includes full documentation for all `/api/funds` endpoints.

---

## 🧪 Endpoints Summary

| Method | Endpoint              | Description                         |
|--------|-----------------------|-------------------------------------|
| GET    | /api/funds            | List funds with optional filters    |
| GET    | /api/funds/{id}       | Get single fund                     |
| GET    | /api/funds/duplicates | List of potential duplicated funds  |
| POST   | /api/funds            | Create new fund                     |
| PUT    | /api/funds/{id}       | Update fund                         |
| DELETE | /api/funds/{id}       | Delete fund                         |

Filters available: `name`, `fund_manager_id`, `start_year`.

---

## 🔄 Event-Driven Process

### Duplicate Detection

A `DuplicateFundWarning` event is dispatched if:

- A fund is created with a name matching:
  - Another fund with the same manager
  - OR an alias of a fund with the same manager

### Listener

- Logs duplicate warnings to `storage/logs/laravel.log`

---

## ⚖️ Design Considerations

- **DRY + SOLID principles** followed in service, request, and controller layers
- **Separation of concerns** between validation, logic, and data access
- **Scalability** through relational DB normalization and indexed queries
- **Testability**: Controllers depend on services and typed request classes
- **Event handling** decouples detection logic from persistence

---

## License

This project is licensed under the [MIT License](LICENSE.txt).
