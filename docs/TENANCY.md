# Multi-Tenancy (Column-based)

This app uses a simple, robust column-based tenancy model:

- Every tenant lives in `tenants` (id, name, domain nullable).
- Most business tables include `tenant_id` and are protected by a global scope so queries are automatically filtered by active tenant.
- New records automatically get `tenant_id` set.
- The active tenant is resolved by middleware:
  1) Admin session switch (`impersonate_tenant_id`)
  2) Authenticated user `users.tenant_id`
  3) Domain match `tenants.domain`

## Admin Switcher
- UI: `Admin → Tenants` to switch active tenant for the session and create new tenants.
- CLI: `php artisan tenant:create "Acme" --domain=acme.example.com`

## Staging/Subdomain Test
1) Create a tenant with `--domain` matching your subdomain.
2) Point DNS (or /etc/hosts) to the server.
3) Visit the subdomain and login — the middleware will pick the tenant by domain.

## Developer Notes
- Tenant-aware models use `App\Models\Concerns\BelongsToTenant` scope.
- Avoid cross-tenant access by always querying via Eloquent models (scope applies automatically).
- To temporarily bypass, clear the tenant in `TenantManager` (not recommended in production).

