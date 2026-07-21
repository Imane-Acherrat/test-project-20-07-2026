# Factory Log Sifter — Module A (Laravel SSR)

A Laravel server-side-rendered application that imports smart-factory sensor
logs from CSV files, filters out normal readings, stores only abnormal
alerts, and lets engineers/administrators review and manage them from a
Blade dashboard.

## Requirements

- PHP 8.2+
- Composer
- MySQL 5.7+ / 8.0
- Laravel CLI tools (`php artisan`)

## Installation

```bash
# 1. Install PHP dependencies
composer install

# 2. Copy environment file and generate app key
cp .env.example .env
php artisan key:generate

# 3. Configure your database in .env
#    DB_DATABASE=factory_log_sifter
#    DB_USERNAME=root
#    DB_PASSWORD=

# 4. Run migrations
php artisan migrate

# 5. Seed default users (1 administrator, 1 engineer)
php artisan db:seed

# 6. Serve the application
php artisan serve
```

The app will be available at `http://localhost:8000`.

### Alternative: import the SQL export directly

Instead of running migrations, you can import the ready-made schema +
seed data from `database/sql/schema.sql`:

```bash
mysql -u root -p < database/sql/schema.sql
```

## Default Accounts (seeded)

| Role          | Email                 | Password |
|---------------|------------------------|----------|
| Administrator | admin@factory.com     | password |
| Engineer      | engineer@factory.com  | password |

## Sample CSV

A sample sensor log file is provided at `database/sql/data-sample.csv` for
testing the import feature (`/sensor-alerts/import`).

## Filtering Rule Recap

- `vibration_amplitude > 80` → abnormal, stored as an alert
  - 81–90 → **Warning**
  - 91+ → **Critical**
- `vibration_amplitude <= 80` → normal, discarded (not stored)

## Routes

| Method | Route                          | Description                          | Access         |
|--------|---------------------------------|---------------------------------------|----------------|
| GET    | `/`                              | Redirect to login or dashboard        | Public         |
| GET    | `/login`                         | Login page                            | Public         |
| POST   | `/login`                         | Authenticate                          | Public         |
| POST   | `/logout`                        | Logout                                | Authenticated  |
| GET    | `/dashboard`                     | Monitoring dashboard                  | Authenticated  |
| GET    | `/sensor-alerts/import`          | CSV upload page                       | Authenticated  |
| POST   | `/sensor-alerts/import`          | Upload & process CSV                  | Authenticated  |
| GET    | `/sensor-alerts`                 | List all stored alerts                | Authenticated  |
| PATCH  | `/sensor-alerts/{id}/status`     | Update alert status                   | Authenticated  |
| DELETE | `/sensor-alerts/{id}`            | Delete an alert                       | Administrator  |
