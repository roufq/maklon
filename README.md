# Maklon - Manufacturing Project Management System

Maklon adalah aplikasi berbasis Laravel untuk operasi contract manufacturing (maklon). Sistem ini mencakup production orders, kepatuhan BPOM, quality control, inventory, deliveries, reporting, dan workflow pendukung (budgets, invoices, teams), dengan keamanan multi-tenant dan akses berbasis role.

## Tentang Maklon

Sistem manajemen lengkap untuk siklus produksi maklon dengan workflow terstruktur:

### Workflow Utama: CS → Finance/Admin → Produksi → BPOM → Box
1. **CS membuat Ticket** dan Production Order (Project) dengan memilih Ticket dari antrian
2. **Pengajuan persetujuan** ke Finance/Admin untuk approval
3. **Produksi memproses** dengan QC gating dan pengajuan sampel
4. **CS mengajukan BPOM** setelah produksi selesai
5. **Pemilihan Box** setelah BPOM aktif

### Fitur Utama

- **Production Orders (Projects)**: CRUD, status, team, customer, BPOM link, approval workflow.
- **Production Stages (Tasks)**: CRUD, assignment, drag-and-drop, capacity fields (`work_station_id`, `scheduled_start/end`), time tracking.
- **BPOM Compliance**: CRUD, penyimpanan dokumen privat, activate/revoke, status tracking, alerts (H-90/30/7), CSV export, audit trail.
- **Quality Control**: Quality Checkpoints + QC Results CRUD, QC gating untuk delivery dan produksi, audit trail, webhooks.
- **Inventory**: Items CRUD, stock movements (in/out/adjust), low-stock alerts, supplier linkage, BOM integration.
- **Suppliers**: CRUD, performance fields, BPOM certification, CSV export.
- **Production Batches**: CRUD, batch number unik per tenant, `qc_status`, audit, webhooks.
- **Deliveries**: CRUD, status flow (pending/ready/shipped/delivered/returned/rejected), QC gating, tracking number.
- **Finance**: Budgets (approve), Invoices (approve/paid, PDF, public link), expense tracking.
- **Reporting**: BPOM compliance, Batch/QC status, Inventory health, Project progress (CSV), Time tracking (CSV), Financial reports, Stakeholder engagement.
- **Tickets & Chat**: Per-project ticketing dengan queue system, messages dalam konteks Ticket/Project.
- **Box Management**: Box Types CRUD, Project Boxes dengan mockup upload.
- **Calendar & Time Tracking**: Event management, time entries dengan timer, utilization reports.
- **API**: Token-based API untuk semua modul utama.
- **Security**: Multi-tenancy, roles/permissions (Spatie), 2FA, API tokens, webhooks dengan HMAC signature.
- **Automation**: Webhooks untuk production/QC events, weekly reports, BPOM expiry alerts, cron jobs.

Aplikasi menggunakan Laravel 12, dengan fitur seperti real-time event broadcasting, background job processing, dan frontend modern yang dibangun dengan Vite.

## Instalasi

Ikuti langkah-langkah berikut untuk mengatur aplikasi Maklon di mesin lokal Anda.

### Prasyarat

- PHP 8.2 atau lebih tinggi
- Composer
- Node.js dan npm
- Database (MySQL, PostgreSQL, SQLite, dll.)

### Langkah Instalasi

1. **Clone repository**:
   ```bash
   git clone <repository-url>
   cd maklon
   ```

2. **Install dependensi PHP**:
   ```bash
   composer install
   ```

3. **Setup file environment**:
   ```bash
   cp .env.example .env
   ```
   Edit `.env` untuk mengkonfigurasi database dan pengaturan lainnya.

4. **Generate application key**:
   ```bash
   php artisan key:generate
   ```

5. **Jalankan database migrations**:
   ```bash
   php artisan migrate --force
   ```

6. **Install dependensi Node.js**:
   ```bash
   npm install
   ```

7. **Build assets**:
   ```bash
   npm run build
   ```

Alternatif, Anda dapat menggunakan script setup yang disediakan:
```bash
composer run setup
```

Seed minimal roles/permissions/tenants (tests auto-seed via `tests/TestCase.php`).

### Menjalankan Aplikasi

Untuk memulai development server, queue worker, dan Vite dev server secara bersamaan:
```bash
composer run dev
```

Ini akan memulai:
- Laravel server di `http://localhost:8000`
- Queue listener
- Vite dev server untuk frontend assets

Schedulers/cron:
- BPOM expiry alerts: `bpom:alert-expiry` (dijadwalkan harian pukul 08:00 via `app/Console/Kernel.php`).
Lihat `docs/CRON_SETUP_CPANEL.md` untuk setup cron server.

### Testing

Jalankan test suite:
```bash
composer run test
```

End-to-end tests mencakup production workflow (Project → Batch → QC → Delivery), BPOM alerts, inventory movements, dan permissions.

File test utama:
- `tests/Feature/E2eProductionWorkflowTest.php`
- `tests/Feature/*Test.php`

## Penggunaan

Setelah instalasi, akses aplikasi di `http://localhost:8000`. Daftar akun baru atau login untuk mulai mengelola project manufaktur Anda.

Fitur utama meliputi:
- Production orders dan stages (capacity fields)
- BPOM registrations (private docs, alerts, export)
- Quality checkpoints & results (QC gating)
- Inventory + stock movements + suppliers (export)
- Deliveries dengan status tracking dan gating
- Budgets, invoices (PDF/public link)
- Reports: BPOM compliance, Batch/QC status, Inventory health, Project progress, Time tracking
- Webhooks untuk production/QC events

## Kontribusi

Kontribusi sangat diterima! Ikuti langkah-langkah berikut:

1. Fork repository
2. Buat feature branch
3. Lakukan perubahan
4. Tambahkan tests jika diperlukan
5. Submit pull request

## Lisensi

Project ini dilisensikan di bawah MIT License.
## Deployment
Lihat docs/DEPLOYMENT.md untuk setup production, optimisasi, backups, dan monitoring.

## Feature Flags
Aplikasi ini menggunakan feature flags untuk mengaktifkan/menonaktifkan modul tertentu. Konfigurasi di `config/features.php`:

- `finance`: Budgets dan invoices
- `calendar`: Event management
- `stakeholders`: Stakeholder management (disabled)
- `surveys`: Stakeholder surveys (disabled)
- `evm`: Earned Value Management (disabled)
- `comments`: Comments system (disabled)
- `risks`: Risk management (disabled)
- `teams`: Team management (disabled)
- `notifications`: Notification system (disabled)
- `resources`: Resource allocation (disabled)
- `attachments`: File attachments (disabled)
- `api_tokens`: API token management (disabled)
- `public_sharing`: Public sharing (disabled)
- `boms`: Bill of Materials (disabled)
- `work_stations`: Work station capacity (disabled)
- `suppliers`: Supplier management (disabled)
- `production_batches`: Production batches (disabled)
- `inventory`: Inventory management (disabled)
- `delivery`: Delivery management (disabled)

## Roles & Permissions
Sistem menggunakan Spatie Laravel Permission dengan roles berikut:

- **Admin**: Akses penuh ke semua fitur
- **Finance**: Approval budgets/invoices, view projects, tickets, QC, BPOM
- **Produksi**: View projects, tickets, QC create/edit, boxes management
- **CS**: Create/view tickets, create projects, view BPOM, boxes view

## API (Overview)

Token-based API (lihat `routes/web.php` di bawah `Route::prefix('api')`):
- `GET /api/projects`, `GET /api/projects/{id}`
- `GET /api/tasks`, `GET /api/tasks/{id}`
- `GET /api/time-entries`
- `GET /api/budgets`
- `GET /api/calendar/events`, `POST /api/calendar/events`
- Penambahan Maklon: `GET /api/bpom`, `GET /api/bpom/{id}`, `GET /api/suppliers`, `GET /api/suppliers/{id}`

## Reports & Exports

- Reports (UI): BPOM compliance, Batch/QC status, Inventory health, Project progress, Time tracking, Financial reports, Stakeholder engagement.
- CSV Exports:
  - Project progress: `reports/project-progress/export`
  - Time tracking: `reports/time-tracking/export`
  - BPOM: `bpom-export`
  - Suppliers: `suppliers-export`

## Webhooks

Events yang dikirim (lihat `app/Jobs/SendWebhookEvent.php`):
- `production.batch.created`, `production.batch.updated`, `production.batch.deleted`
- `qc.result.created`, `qc.result.updated`, `qc.result.deleted`

Konfigurasi di Settings → Webhooks. Optional HMAC signature via shared secret header `X-Webhook-Signature`.

## Operasi

- Deployment: lihat `docs/DEPLOYMENT.md`
- Backups: `scripts/backup/db-backup.sh`
- Feature Flags: `docs/FEATURE_FLAGS.md`
- Performance: `docs/PERF_TEST_PLAN.md`
- UAT: `docs/UAT_CHECKLIST.md`
- Rollback: `docs/ROLLBACK_PLAN.md`
- Training: `docs/TRAINING.md`
- Tenancy: `docs/TENANCY.md`
- Cron Setup: `docs/CRON_SETUP_CPANEL.md`
