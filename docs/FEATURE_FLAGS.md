Feature Flags

- Define feature flags in `.env` as `FEATURE_X=true|false` and read via `config('app.features.X')`.
- Suggested flags: `production_webhooks`, `bpom_exports`, `supplier_exports`, `qc_audit_logging`.
- Wrap non-critical integrations with `if (config('app.features.production_webhooks', true)) { ... }`.

