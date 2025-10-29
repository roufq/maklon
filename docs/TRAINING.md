Training Guide (Maklon)

Audience: Admin, Production Manager, QC, Warehouse, Client

1) Login & Roles
- Roles: Admin, ProductionManager, QC, Warehouse, Client
- 2FA (optional): enable in Settings → Security → 2FA

2) Production Orders & Stages
- Create project (Production Order) with customer & BPOM link
- Add tasks (stages), assign work station and schedule start/end
- Update task status via Kanban

3) BPOM Management
- Create BPOM: upload document (stored privately)
- Activate/Revoke; watch expiry badges and alerts
- Export list via BPOM Export

4) Quality Control
- Define Quality Checkpoints per product/order
- Record QC Results (pass/fail)
- QC gating: delivery blocked if batch not passed

5) Inventory & Suppliers
- Create items, set min stock, link supplier
- Record stock movements (in/out/adjust)
- Export suppliers list

6) Production Batches & Deliveries
- Create batch linked to order & BPOM
- After QC passed, create delivery; track status and optional tracking number

7) Finance & Reporting
- Budgets: create/approve categories & expenses
- Invoices: approve/mark paid, PDF, public link
- Reports: BPOM compliance, Batch/QC status, Inventory health

8) API & Webhooks
- API tokens: Settings → API Tokens
- Webhooks: configure event endpoints; optional HMAC signature header

9) Operations
- Backups: scripts/backup/db-backup.sh
- Deploy: docs/DEPLOYMENT.md; Performance: docs/PERF_TEST_PLAN.md
- Rollback: docs/ROLLBACK_PLAN.md; Feature Flags: docs/FEATURE_FLAGS.md

