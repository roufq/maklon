Performance Test Plan

- Key pages: dashboard, projects index/show, batches index, reports (bpom, batch-qc, inventory-health)
- Target: P95 < 800ms on production hardware
- Steps:
  - Enable `LOG_CHANNEL=daily`, set `APP_DEBUG=false`
  - Warm caches: `composer run prod:optimize`
  - Use `php artisan route:trans:list` to ensure routes cached
  - Run ApacheBench/k6 for 200–500 VU with realistic think time
  - Monitor DB slow queries; add missing indexes as needed

