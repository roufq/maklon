# TODO: Adaptasi Aplikasi ke Sistem Manajemen Maklon + BPOM Compliance (Revisi)

## Overview
Adaptasi aplikasi project management menjadi sistem manajemen subcontract manufacturing (maklon) dengan kepatuhan BPOM. Fokus pada reuse komponen inti (projects/tasks/users), penambahan entitas produksi, kontrol kualitas, dan pelacakan registrasi BPOM dengan keamanan data multi-tenant.

- Scope: 60% reuse, 40% pengembangan baru
- Timeline: 8 minggu (5 sprints)
- Fokus: Production orders, BPOM tracking, supplier management, inventory, QC, delivery

## Prinsip Desain (Guiding Principles)
- Alias UI terlebih dahulu, tanpa rename besar class/file internal (minim risiko). Route/label baru → “Production Orders” (projects) dan “Production Stages” (tasks).
- Semua tabel baru wajib punya `tenant_id` + global scope tenant (mencegah kebocoran data).
- Dokumen BPOM disimpan pada storage privat; akses via route terproteksi dan diaudit.
- Audit trail untuk perubahan data regulasi (BPOM), batch, dan QC (create/update/delete).
- Permissions granular dengan Spatie (role-based), siap untuk hardening.

## Phase 1: Core Mapping & Reuse (Sprint 1 - 1 minggu)

### 1. Terminology & UI (Alias, tanpa rename internal)
- [x] Tambahkan label/route UI “Production Orders” (alias projects) dan “Production Stages” (alias tasks)
- [x] Update menu/breadcrumb/page titles (tanpa ganti nama class/file)
- [x] Tambah section "Maklon Management" di sidebar

### 2. Database Schema (Minimal Viable)
- [x] Projects: +`customer_id:users`, `order_quantity:int`, `due_date:date`, `production_status:enum(draft,scheduled,in_progress,blocked,completed)`, `tenant_id` (wajib)
- [x] Tasks: +`stage_type:enum(cutting,mixing,filling,packaging,qc)`, `estimated_hours:decimal(8,2)`, `qc_required:boolean`
- [x] Buat `bpom_registrations`: `id, product_name, registration_number UNIQUE, approval_date, expiry_date, status(enum:active,expired,revoked,pending), document_path, tenant_id`
- [x] Buat `production_batches`: `id, project_id, bpom_registration_id, batch_number UNIQUE per tenant, quantity_produced, expiry_date, qc_status(enum:pending,passed,failed), tenant_id`

### 3. Model Updates
- [x] Project/Task: tambahkan fillable + casts + relasi
- [x] BpomRegistration, ProductionBatch: model + relasi (Project hasMany ProductionBatch)
- [x] Pastikan scope tenant aktif pada model-model baru

## Phase 2: BPOM & Supplier Management (Sprint 2 - 2 minggu)

### 4. BPOM Registration Management
- [x] BpomRegistrationController (CRUD)
- [x] Views: index, create, edit, show
- [x] Upload dokumen ke storage privat (download via route terproteksi)
- [x] Expiry monitoring + alerts (scheduler H-90/H-30/H-7)
- [x] Status machine: draft → active → expired/revoked
- [x] Audit log: create/update/delete + download

### 5. Supplier Management (MVP)
- [x] suppliers (tabel baru) ATAU reuse users+stakeholders jika cukup
- [x] SupplierController (CRUD)
- [x] Atribut dasar: type, bpom_certified, contact_info, rating/performance (baseline)

### 6. Production Batch Tracking
- [x] ProductionBatchController
- [x] Link batch ke production orders
- [x] Nomor batch auto (unik per-tenant)
- [x] QC status per batch (pending/passed/failed)

## Phase 3: Inventory & Production Management (Sprint 3 - 2 minggu)

### 7. Inventory (MVP)
- [x] inventory_items: `id, name, supplier_id, current_stock, min_stock, unit, unit_cost, tenant_id`
- [x] InventoryController (CRUD)
- [x] Stock movements (in/out) + low stock (baseline UI; alert otomatis menyusul)

### 8. BOM (v1 JSON)
- [x] boms: `id, product_name, materials JSON(id,qty,unit), version, total_cost, tenant_id`
- [x] BomController + views
- [x] Hitung biaya material dasar (cache total_cost)

### 9. Capacity Planning (v1)
- [x] work_stations: `id, name, capacity_per_hour, status, tenant_id`
- [x] Penjadwalan sederhana per mesin/operator (assign work_station_id, scheduled_start/end di Task)

### 10. Quality Control (MVP)
- [x] quality_checkpoints + qc_results
- [x] QC form per stage; status pass/fail
- [x] QC gating: blokir delivery jika batch belum `passed`

## Phase 4: Customer & Delivery Management (Sprint 4 - 2 minggu)

### 11. Customer Order Management
- [~] Customers via users ber‑role Client (reuse) — approval/feedback/satisfaction/portal menyusul

### 12. Delivery Management
- [x] Create delivery tracking system (Deliveries CRUD)
- [x] Add shipping information fields
- [x] Add delivery confirmation (mark delivered)
- [x] Add return/reject management (status actions)
- [x] Add delivery status reports
- [x] Add logistics partner integration

### 13. Integration dengan Existing Features
- [x] Link BPOM registration ke projects (products)
- [x] Add BPOM fields ke project creation form
- [x] Update invoice system untuk include BPOM info
- [x] Add BPOM compliance reports
- [x] Integrate inventory dengan budget system

## Phase 5: Reporting & Finalization (Sprint 5 - 1 minggu)

### 14. Reporting (prioritas tinggi)
- [x] BPOM compliance (aktif/kedaluwarsa/akan kedaluwarsa)
- [x] Batch & QC status per production order
- [x] Inventory low stock & turnover dasar

### 15. Testing & Refinement
- [x] E2E workflow produksi (orders → stages → batch → QC → delivery)
- [x] Validasi alur input BPOM (audit & akses dokumen)
- [x] Uji inventory & stock movement
- [x] Uji QC gating
- [x] Perf test halaman kunci (target P95 < 800ms)
- [x] UAT

## Technical Requirements

### Database Migrations
- [x] Semua tabel baru pakai `tenant_id` + index
- [x] FK constraints dan index pada kolom relasi & nomor unik (registration_number, batch_number)
- [x] Seeder sample data (BPOM, suppliers, inventory)
- [x] Backup sebelum migrasi besar

### Security & Permissions (Spatie)
- [x] Tambah permissions: `production.view|create|edit|delete`, `bpom.view|create|edit|delete`, `inventory.view|create|edit|delete`, `qc.view|create|edit|delete`, `delivery.view|create|edit|delete`, `supplier.view|create|edit|delete`
- [x] Role matrix: Admin (all), ProductionManager (production.*, qc.*, bpom.view, inventory.view), QC (qc.*, production.view), Warehouse (inventory.*, delivery.*), Client (tracking view)
- [x] Audit logging untuk BPOM, supplier, batch, QC

### API & Integrations
- [x] Update existing API endpoints untuk maklon
- [x] Add BPOM-specific API endpoints
- [x] Add supplier management API
- [x] Update webhook system untuk production events
- [x] Add export functionality untuk BPOM & supplier reports

## Success Criteria (terukur)
- [x] Production Order → Stages → Batch → QC → Delivery berjalan end-to-end
- [x] BPOM registration + dokumen privat + alert H-90/H-30/H-7 aktif
- [x] QC gating mengunci delivery bila belum `passed`
- [x] Inventory low-stock alert dan movement tercatat
- [x] 3 laporan inti tersedia (BPOM, Batch/QC, Inventory)
- [ ] P95 < 800ms pada halaman kunci; tidak ada kebocoran tenant
- [ ] Materi training diperbarui

## Risk Mitigation
- [x] Regular backups sebelum major changes
- [x] Feature flags untuk gradual rollout
- [x] Comprehensive testing sebelum production deploy
- [x] Rollback plan jika issues ditemukan

## Dependencies
- Laravel 12 framework
- Existing database schema
- File upload system untuk BPOM documents & supplier docs
- Email system untuk alerts & notifications
- Existing authentication & authorization

## Timeline Summary
- **Week 1**: Core mapping & basic setup
- **Week 2-3**: BPOM & supplier management
- **Week 4-5**: Inventory & production planning
- **Week 6-7**: Customer & delivery management
- **Week 8**: Reporting, testing & deployment

## Estimated Effort (Updated)
- **Development**: 8 weeks (1-2 developers)
- **Design**: 1 week (UI/UX updates)
- **Testing**: 2 weeks (QA)
- **Training**: 1 week (user documentation)
- **Total Cost**: $8,000-15,000
