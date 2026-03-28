# Dmstic — Roadmap

**Author**: Dariusz Porczyński
**Updated**: 2026-03-28
**Project**: Dmstic — Analiza kosztów domowych (Household Cost Analytics)

---

## Version History & Plan

---

## v0.1 — Foundation (Released 2026-03-28)

Core application with real production data from two utility providers.

### Infrastructure
- [x] Laravel 13.2 deployment on VM7000 (Virtualmin, Apache CGI mode, PHP 8.3)
- [x] MySQL schema: `energy_providers`, `bills`, `monthly_summary`, `credentials`, `settings`
- [x] SSL via wildcard `*.netol.com` (ZeroSSL) terminated on HAProxy ns2
- [x] DNS: `dmstic.netol.com → 206.189.31.117`
- [x] HAProxy routing fix — HTTP/2 coalescing 503 bug resolved
- [x] Public GitHub repository: https://github.com/Dmstic/dmstic

### Data Import
- [x] TAURON electricity data scraper — 40 documents, 38 618 kWh, 35 269 PLN (2021–2025)
- [x] ORLEN/PGNiG gas data scraper — 29 documents, 30 804 kWh, 11 673 PLN

### UI & Navigation
- [x] Dashboard with stacked bar chart (Chart.js 4.4.0) and doughnut chart
- [x] Dynamic left sidebar with all providers
- [x] Dark / Light / System theme toggle
- [x] Per-provider pages with filter bar
- [x] Settings page (app_name, color_scheme, ai_api_key)
- [x] Documentation page (`/docs`)
- [x] Admin: Add Provider form, Edit Provider form

### Analytics
- [x] Monthly stats table and chart (PLN, kWh, cost/kWh)
- [x] Year-over-year pivot table
- [x] Period-to-period comparison (two arbitrary date ranges)
- [x] Filters: date range, document type (FV/FK/NO/NB), status

---

## v0.2 — Enhanced UX (In Progress)

Focus: developer workflow, document handling, UI improvements.

### CI/CD
- [ ] Self-hosted GitHub Actions runner on VM7000
- [ ] Automated deployment workflow on push to `main`
- [ ] Runner configured for `Dmstic/dmstic` repository

### Credentials Management
- [ ] Private repo (`porczynski/dmstic`) with `credentials.json`
- [ ] App fetches credentials from private repo via GitHub PAT at runtime
- [ ] Private repo token stored in `settings` table

### Document Viewer
- [ ] Inline PDF viewer on provider page (iframe or PDF.js)
- [ ] Document list with clickable entries
- [ ] PDFs stored in `storage/app/public/documents/{provider_id}/`
- [ ] PDF download for TAURON invoices (endpoint discovered, pending execution)
- [ ] PDF download for ORLEN invoices

### Sidebar Improvements
- [ ] Inline editing — click provider name to rename
- [ ] Inline color picker — click color swatch to change
- [ ] Inline icon selector
- [ ] Changes saved via AJAX (no page reload)

### Date Filters
- [ ] Quick year-select buttons on provider filter bar (2021, 2022, 2023, 2024, 2025)
- [ ] Improved date picker UX

### Forecasting
- [ ] Historical data trend line (linear regression)
- [ ] Forecast extension on monthly chart (dashed line for future months)
- [ ] Cost forecast based on predicted kWh × average cost/kWh

### Theme
- [ ] Configurable primary accent color (stored in settings)
- [ ] Font selection (system fonts)
- [ ] Layout options (sidebar width)

---

## v0.3 — More Providers & AI Parsing (Planned)

### AI Document Parsing
- [ ] Upload PDF invoice via admin interface
- [ ] Send to Claude API for field extraction
- [ ] Pre-populate form with extracted values: doc_number, doc_type, dates, amounts, kWh
- [ ] User confirms and saves bill record

### New Providers
- [ ] Water utility integration (manual PDF import or scraper TBD)
- [ ] Internet/Multimedia provider (scraper TBD)
- [ ] Bank statement import (OFX / MT940 / CSV file upload)

### Provider Scraper Improvements
- [ ] TAURON PDF bulk download (server availability pending)
- [ ] ORLEN PDF download
- [ ] Scheduled scraping (Artisan command + cron / supervisor)

### Notifications
- [ ] Email alert when new unpaid invoice detected
- [ ] Alert when monthly usage exceeds threshold

---

## v1.0 — Production-Ready (Future)

### Multi-User Support
- [ ] User authentication (Laravel Breeze or Fortify)
- [ ] Per-user providers and bills
- [ ] Admin role for managing providers/users

### Reports & Export
- [ ] CSV export (bills list, monthly summary)
- [ ] PDF report generation (monthly/annual summary)
- [ ] Printable chart views

### Budget Tools
- [ ] Monthly budget targets per provider
- [ ] Budget vs actual comparison
- [ ] Over-budget alerts

### Comparison
- [ ] Compare own usage against national averages (where data available)
- [ ] Historical cost trend vs inflation index

### Mobile
- [ ] Responsive layout improvements for mobile browsers
- [ ] Progressive Web App (PWA) manifest

### API
- [ ] JSON API endpoints for external integrations (Home Assistant, etc.)
- [ ] API key authentication

---

## Feature Ideas (Backlog / No Commitment)

- Home Assistant integration (push sensor data)
- Carbon footprint estimation per kWh
- Multi-currency support
- Import from energy.gov.pl or similar public datasets
- OCR for scanned invoices (not just digital PDFs)
- Slack / Telegram notifications
- Dark/light auto-switch based on time of day

---

**Author**: Dariusz Porczyński
**Last updated**: 2026-03-28
