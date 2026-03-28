# Dmstic — TODO List

**Author**: Dariusz Porczyński
**Updated**: 2026-03-28
**Project**: Dmstic — Analiza kosztów domowych (Household Cost Analytics)

Items are organised by priority and category. Each item references the version it belongs to.

---

## Priority: Critical (Blocking v0.2)

### CI/CD — GitHub Actions runner

- [ ] Install self-hosted GitHub Actions runner on VM7000 as `dmstic` user
- [ ] Create `.github/workflows/deploy.yml` in `Dmstic/dmstic`
  - Trigger: push to `main`
  - Steps: `git pull`, `composer install --no-dev`, `php artisan config:cache`, `view:cache`, `route:cache`
- [ ] Register runner with `Dmstic/dmstic` repository token
- [ ] Test full deploy pipeline end-to-end

### Private Repo — Credentials

- [ ] Set up `porczynski/dmstic` private repo on GitHub
- [ ] Create `credentials.json` from `credentials.json.example` with real values
- [ ] Generate GitHub PAT with `repo` scope for runtime fetch
- [ ] Store PAT in `settings` table via `/settings` page (key: `private_repo_token`)
- [ ] Implement `CredentialService::fetch()` — GitHub Contents API call + base64 decode + JSON parse
- [ ] Wire credential service into `TauronScraper` and `OrlenScraper`

---

## Priority: High (v0.2 Core Features)

### Document Downloads

- [ ] TAURON PDF download — endpoint: `GET /api/sitecore/EbokArchiveDocuments/GetDocumentImage`
  - Server was unavailable during initial import — retry
  - Save PDFs to `storage/app/public/documents/1/{doc_number}.pdf`
  - Update `bills.file_path` for downloaded files
- [ ] ORLEN PDF download — requires authenticated session with cookies
  - Use `OrlenScraper` session to download each invoice PDF
  - Save to `storage/app/public/documents/2/{doc_number}.pdf`

### Document Viewer

- [ ] Add document list section to `provider/show.blade.php`
- [ ] Show bills with `file_path` set as clickable entries
- [ ] Implement inline PDF viewer (iframe pointing to `/storage/documents/{provider_id}/{file}`)
- [ ] Fallback: download link if inline display fails
- [ ] Ensure `php artisan storage:link` has been run on VM7000

### Date Filter UX

- [ ] Add quick year-select buttons to filter bar on `/provider/{id}`
  - Buttons: "2021", "2022", "2023", "2024", "2025"
  - Clicking a year sets `from=YYYY-01-01&to=YYYY-12-31` and reloads
- [ ] Highlight active year button when its range matches current filter
- [ ] Add "All time" / "Clear" button to reset filters

### Sidebar — Inline Editing

- [ ] Add `contenteditable` to provider name in sidebar (click to edit)
- [ ] Add color picker to color swatch (click to open)
- [ ] Add icon selector modal (click icon to change)
- [ ] AJAX endpoint: `POST /admin/provider/{id}/inline-edit` (returns updated values)
- [ ] Update sidebar DOM without page reload after save
- [ ] Add route in `routes/web.php`

---

## Priority: Medium (v0.2 and v0.3)

### Forecasting

- [ ] Compute linear regression on monthly kWh data (last 12–24 months)
- [ ] Add forecast data array to `ProviderController@show` (next 3–6 months)
- [ ] Extend monthly chart with dashed forecast line
- [ ] Tooltip: distinguish historical vs forecast data points
- [ ] Display forecast cost estimate (predicted kWh × average cost/kWh)

### Theme Customisation

- [ ] Add `accent_color` setting (hex value, stored in settings)
- [ ] Apply to CSS custom properties in `layout.blade.php`
- [ ] Add font selection setting (system-ui, Georgia, monospace)
- [ ] Preview changes live on settings page (JS)

### AI Document Parsing (v0.3)

- [ ] `POST /admin/upload` — accept PDF file upload
- [ ] Read file, base64 encode, send to Claude API messages endpoint
- [ ] Prompt: extract doc_number, doc_type, issue_date, due_date, amount_gross, amount_net, kwh
- [ ] Return JSON with extracted fields to frontend
- [ ] Show pre-populated confirmation form
- [ ] On confirm: `INSERT INTO bills (...)` with extracted + user-corrected values
- [ ] Store original PDF in `storage/app/public/documents/{provider_id}/`
- [ ] Handle Claude API errors gracefully (fall back to manual entry)

### Water Provider (v0.3)

- [ ] Research local water utility portal/API
- [ ] If no API: implement file upload + AI parsing flow
- [ ] Create `WaterScraper` class (or `WaterImporter` if file-based)
- [ ] Add provider record via admin UI

### Internet/ISP Provider (v0.3)

- [ ] Research Multimedia Polska or configured ISP portal
- [ ] Implement scraper or file import
- [ ] Create `MultimediaScraper` class

### Bank Statements (v0.3)

- [ ] Implement OFX / MT940 / CSV file upload
- [ ] Parser for each format
- [ ] AI parsing as fallback for unsupported formats
- [ ] Map bank transactions to bill records (by amount + date range)

---

## Priority: Low (v1.0 and future)

### Authentication

- [ ] Laravel Breeze or Fortify for login/register
- [ ] Single-user mode (current) → multi-user with per-user data isolation
- [ ] Admin role for managing providers
- [ ] Session expiry configuration

### Export

- [ ] CSV export of bills table (with applied filters)
- [ ] CSV export of monthly summary
- [ ] PDF report: monthly/annual summary with charts (use Browsershot or dompdf)

### Email Notifications

- [ ] Configure Laravel mail (SMTP settings in settings table)
- [ ] Alert: new unpaid invoice detected (triggered on import)
- [ ] Alert: monthly usage exceeds configured threshold
- [ ] Weekly summary digest (optional)

### Budget Planning

- [ ] `budgets` table: provider_id, year, month, budget_pln
- [ ] Budget input UI on provider page
- [ ] Budget vs actual bar chart overlay
- [ ] Over-budget highlight in monthly stats table

### Mobile Responsiveness

- [ ] Audit all pages for mobile breakpoints
- [ ] Sidebar: collapse to hamburger menu on small screens
- [ ] Charts: responsive sizing with Chart.js `responsive: true`
- [ ] Filter bar: stack vertically on mobile

### API Endpoints

- [ ] `GET /api/providers` — list providers with aggregated totals
- [ ] `GET /api/providers/{id}/bills` — paginated bills with filters
- [ ] `GET /api/providers/{id}/monthly` — monthly summary data
- [ ] API key authentication (stored in settings)
- [ ] Home Assistant integration documentation

---

## Technical Debt & Maintenance

- [ ] HAProxy regex bug: `netol.com` without anchor — may match unintended hostnames; discuss with infrastructure team
- [ ] Add database indexes: `bills.issue_date`, `bills.provider_id`, `bills.doc_type` (review query plan)
- [ ] Add `bills.deleted_at` for soft deletes (avoid hard deletes on import re-runs)
- [ ] Artisan command: `dmstic:import {provider}` — proper command class, not ad-hoc script
- [ ] Artisan command: `dmstic:rebuild-monthly-summary` — for re-aggregation after bulk imports
- [ ] Add Laravel Telescope for development debugging (dev environment only)
- [ ] Review and tighten `AppServiceProvider` view composer — N+1 risk on large provider lists
- [ ] Add `.env.example` to public repo with all required keys documented
- [ ] PHPStan static analysis at level 5+ (set up in CI/CD)
- [ ] Basic feature tests: dashboard loads, provider page loads, settings save

---

## Documentation

- [x] `docs/README.md` — public documentation (this release)
- [x] `docs/ROADMAP.md` — versioned feature plan
- [x] `docs/TODO.md` — this file
- [x] `docs/CONTRIBUTING.md` — contribution guide
- [ ] `docs/SCREENSHOTS.md` — application screenshots (v0.2)
- [ ] `docs/PROVIDERS.md` — per-provider setup guides (TAURON, ORLEN, etc.)
- [ ] `docs/API.md` — API endpoint reference (v1.0)

---

**Author**: Dariusz Porczyński
**Last updated**: 2026-03-28
