# Maklon - Manufacturing Project Management System

Maklon is a Laravel-based application for contract manufacturing (maklon) operations. It covers production orders, BPOM compliance, quality control, inventory, deliveries, reporting, and supporting workflows (budgets, invoices, teams), with multi‑tenant security and role‑based access.

## About Maklon

End‑to‑end lifecycle for maklon production:

- **Production Orders (Projects)**: CRUD, status, team, client, BPOM link.
- **Stages (Tasks)**: CRUD, assignment, drag‑and‑drop, capacity fields (`work_station_id`, `scheduled_start/end`).
- **BPOM Compliance**: CRUD, private document storage, activate/revoke, status tracking, alerts (H‑90/30/7), CSV export.
- **Quality Control**: Quality Checkpoints + QC Results CRUD, QC gating of delivery, audit trail, webhooks.
- **Inventory**: Items CRUD, stock movements (in/out/adjust), low‑stock visibility, supplier linkage.
- **Suppliers**: CRUD, performance fields, CSV export.
- **Production Batches**: CRUD, batch number, `qc_status`, audit, webhooks.
- **Deliveries**: CRUD, status flow (pending/ready/shipped/delivered/returned/rejected), QC gating.
- **Finance**: Budgets (approve), Invoices (approve/paid, PDF, public link).
- **Reporting**: BPOM compliance, Batch/QC status, Inventory health, Project progress (CSV), Time tracking (CSV).
- **API**: Authenticated token API for Projects/Tasks/Time/Budgets/Calendar + Maklon endpoints (BPOM, Suppliers).
- **Security**: Multi‑tenancy, roles/permissions (Spatie), 2FA, API tokens.
- **Automation**: Webhooks for production/QC events, weekly report jobs.

The application uses Laravel 12, with features like real-time event broadcasting, background job processing, and a modern frontend built with Vite.

## Installation

Follow these steps to set up the Maklon application on your local machine.

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and npm
- A database (MySQL, PostgreSQL, SQLite, etc.)

### Step-by-Step Installation

1. **Clone the repository**:
   ```bash
   git clone <repository-url>
   cd maklon
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Set up environment file**:
   ```bash
   cp .env.example .env
   ```
   Edit `.env` to configure your database and other settings.

4. **Generate application key**:
   ```bash
   php artisan key:generate
   ```

5. **Run database migrations**:
   ```bash
   php artisan migrate --force
   ```

6. **Install Node.js dependencies**:
   ```bash
   npm install
   ```

7. **Build assets**:
   ```bash
   npm run build
   ```

Alternatively, you can use the provided setup script:
```bash
composer run setup
```

Seed minimal roles/permissions/tenants (tests auto‑seed via `tests/TestCase.php`).

### Running the Application

To start the development server, queue worker, and Vite dev server concurrently:
```bash
composer run dev
```

This will start:
- Laravel server on `http://localhost:8000`
- Queue listener
- Vite dev server for frontend assets

Schedulers/cron:
- BPOM expiry alerts: `bpom:alert-expiry` (scheduled daily 08:00 via `app/Console/Kernel.php`).
See `docs/CRON_SETUP_CPANEL.md` for server cron setup.

### Testing

Run the test suite:
```bash
composer run test
```

End‑to‑end tests cover production workflow (Project → Batch → QC → Delivery), BPOM alerts, inventory movements, and permissions.

Key test files:
- `tests/Feature/E2eProductionWorkflowTest.php`
- `tests/Feature/*Test.php`

## Usage

After installation, access the application at `http://localhost:8000`. Register a new account or log in to start managing your manufacturing projects.

Key features include:
- Production orders and stages (capacity fields)
- BPOM registrations (private docs, alerts, export)
- Quality checkpoints & results (QC gating)
- Inventory + stock movements + suppliers (export)
- Deliveries with status tracking and gating
- Budgets, invoices (PDF/public link)
- Reports: BPOM compliance, Batch/QC status, Inventory health, Project progress, Time tracking
- Webhooks for production/QC events

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is licensed under the MIT License.
## Deployment
See docs/DEPLOYMENT.md for production setup, optimization, backups, and monitoring.
## API (Overview)

Token‑based API (see `routes/web.php` under `Route::prefix('api')`):
- `GET /api/projects`, `GET /api/projects/{id}`
- `GET /api/tasks`, `GET /api/tasks/{id}`
- `GET /api/time-entries`
- `GET /api/budgets`
- `GET /api/calendar/events`, `POST /api/calendar/events`
- Maklon additions: `GET /api/bpom`, `GET /api/bpom/{id}`, `GET /api/suppliers`, `GET /api/suppliers/{id}`

## Reports & Exports

- Reports (UI): BPOM compliance, Batch/QC status, Inventory health, Project progress, Time tracking.
- CSV Exports:
  - Project progress: `reports/project-progress/export`
  - Time tracking: `reports/time-tracking/export`
  - BPOM: `bpom-export`
  - Suppliers: `suppliers-export`

## Webhooks

Events dispatched (see `app/Jobs/SendWebhookEvent.php`):
- `production.batch.created`, `production.batch.updated`, `production.batch.deleted`
- `qc.result.created`, `qc.result.updated`, `qc.result.deleted`

Configure in Settings → Webhooks. Optional HMAC signature via shared secret header `X-Webhook-Signature`.

## Operations

- Deployment: see `docs/DEPLOYMENT.md`
- Backups: `scripts/backup/db-backup.sh`
- Feature Flags: `docs/FEATURE_FLAGS.md`
- Performance: `docs/PERF_TEST_PLAN.md`
- UAT: `docs/UAT_CHECKLIST.md`
- Rollback: `docs/ROLLBACK_PLAN.md`
- Training: docs/TRAINING.md
