# IT15-L Enrollment System (Backend)

Laravel 12 REST API for student enrollment, courses, dashboard analytics, weather-ready integration, and authentication.

## Project Overview

This backend powers the Enrollment System frontend and provides:

- Authentication using Laravel Sanctum
- Student, Course, Enrollment, and School Day APIs
- Dashboard analytics endpoints (charts and activity feed data)
- Database seeders for large demo datasets

## Tech Stack

- PHP 8.2+
- Laravel 12
- Laravel Sanctum
- MySQL (recommended) or PostgreSQL
- Composer

## Prerequisites

Install the following before setup:

- PHP 8.2 or newer
- Composer 2+
- MySQL 8+ (or PostgreSQL)
- Node.js 18+ and npm (required for Vite assets in Laravel)

## Backend Setup

Run these commands from this backend folder (`IT15-L-Backend`):

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Backend will run at:

```text
http://127.0.0.1:8000
```

## Environment Configuration

`.env.example` is included and contains required variable names.

Important variables:

- `APP_URL`
- `FRONTEND_URL`
- `DB_CONNECTION`
- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- `FORCE_HTTPS`
- `REQUIRE_HTTPS_FOR_API`
- `SANCTUM_TOKEN_EXPIRATION`
- `THIRD_PARTY_API_KEY`
- `THIRD_PARTY_API_URL`

## API Authentication

Default seeded admin account:

- Email: `admin@example.com`
- Password: `admin12345`

Login endpoint:

```text
POST /api/login
```

Use the returned bearer token for protected endpoints.

## Frontend Setup (React App)

If you are running the paired frontend (`HERRERA-react-app`), use:

```bash
cd ../IT15-L-Frontend/HERRERA-react-app
npm install
cp .env.example .env
npm run dev
```

Frontend default URL:

```text
http://localhost:5173
```

Set frontend API base URL in frontend `.env`:

```text
VITE_API_BASE_URL=http://127.0.0.1:8000/api
```

## Useful Commands

```bash
php artisan test
php artisan db:seed
php artisan route:list
```


