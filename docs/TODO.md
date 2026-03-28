# Dmstic — TODO List

**Author:** Dariusz Porczyński
**Last updated:** 2026-03-28
**Project:** dmstic (https://dmstic.netol.com → future: dmstic.com)

---

## [CRITICAL — current sprint / v0.3]

These items are blocking or directly requested. Complete before moving to next sprint.

- [ ] **Period filter on top of Przegląd page** — date range picker (from/to), applies to all charts and tables on the overview page
- [ ] **Period filter on top of each provider page** — same date range picker component, reusable, works independently per provider view
- [ ] **Provider edit: all fields editable** — editing panel must allow changing: name, type, login, password, API endpoint, account number, notes
- [ ] **Provider edit: delete provider** — confirmation dialog, removes provider and all associated data from DB
- [ ] **Provider edit: delete all bills** — option to delete all bill data for a provider without deleting the provider itself
- [ ] **Update main README.md** — replace Laravel boilerplate with simple deployment instructions; keep technical docs in `docs/README.md`
- [ ] **MPWiK Wrocław scraper** — implement scraper for https://ebok.mpwik.wroc.pl/trust/pulpit; fetch invoices, parse amounts and dates
- [ ] **All documentation in English** — migrate all doc files from Polish to English; UI strings remain in lang/ for i18n

---

## [HIGH — next sprint / v0.4]

Authentication, multi-tenancy, and admin panel foundation.

- [ ] **Laravel Breeze authentication** — install and configure; registration, login, logout, forgot password, reset password
- [ ] **Email verification flow** — require verified email before accessing dashboard
- [ ] **Multi-tenancy: add user_id to all tables** — energy_providers, bills, monthly_summary, documents; global Eloquent scopes
- [ ] **Data migration: assign existing data to first user** — migration script for existing single-user data
- [ ] **Gmail SMTP configuration** — configure .env, test delivery; see docs/GMAIL-SMTP-SETUP.md
- [ ] **WelcomeEmail Mailable** — sent on registration
- [ ] **EmailVerification Mailable** — custom branded verification email
- [ ] **PasswordReset Mailable** — custom branded reset email
- [ ] **Basic admin panel (Filament v3)** — install Filament, create AdminPanelProvider, basic user list
- [ ] **LLM API key in admin panel** — system_settings table, Filament resource, encrypted storage
- [ ] **User storage isolation** — per-user file storage at `storage/app/private/users/{user_id}/`
- [ ] **Subscription plan column on users** — free/pro, enforced via SubscriptionService

---

## [MEDIUM — v0.5 sprint]

Feature completeness, LLM integration, and full admin panel.

- [ ] **Multi-language support (en/pl)** — lang/ directory structure, SetLocale middleware, language switcher in header
- [ ] **User locale in profile** — users can set preferred language; stored in users.locale column
- [ ] **Filament admin panel: full features** — user management, suspend/delete, subscription changes, audit log
- [ ] **Subscription tiers enforcement** — Free: 2 providers max, 100 bills max; Pro: unlimited + AI parsing
- [ ] **LLM document parsing** — PDF text extraction (smalot/pdfparser), LLMService, DocumentUploadJob
- [ ] **Claude API integration** — Anthropic claude-3-5-sonnet support in LLMService
- [ ] **OpenAI integration** — optional GPT-4o support in LLMService
- [ ] **Local Ollama LLM integration** — http://ollama.netol.io:11434 for free parsing (NETOL infra)
- [ ] **Per-user AI parsing quota** — track monthly usage, enforce Pro-only gate
- [ ] **Background job: auto-fetch documents** — FetchAllProvidersJob on daily schedule
- [ ] **Laravel Queue with database driver** — queue worker for emails and scraper jobs
- [ ] **TAURON PDF auto-download** — background job to download latest invoices from TAURON portal
- [ ] **ORLEN PDF auto-download** — background job to download from ebok.myorlen.pl
- [ ] **WeeklyReport email** — optional Pro feature, Sunday 8am scheduled report
- [ ] **CSV/Excel export** — Pro feature; export bills data to CSV or XLSX
- [ ] **Provider scraper: plug-in architecture** — config/scrapers.php, ScraperInterface, per-provider implementations

---

## [LOW / FUTURE — post v1.0]

Post-launch growth features and infrastructure.

- [ ] **Migrate to dmstic.com** — DNS change, Cloudflare, HAProxy config update; no app code changes
- [ ] **Public landing page** — marketing page at dmstic.com with feature overview, pricing, signup CTA
- [ ] **Stripe/Paddle payment integration** — self-service Pro upgrade; webhooks for plan management
- [ ] **Mobile app (PWA)** — Progressive Web App manifest, service worker, offline support
- [ ] **REST API for external integrations** — Laravel Sanctum, API tokens, documented endpoints
- [ ] **White-label option** — custom branding per account (logo, colors, domain)
- [ ] **ENEA scraper** — energy provider scraper
- [ ] **PGNiG/PSG scraper** — gas provider scraper
- [ ] **ORANGE/PLAY/T-Mobile scraper** — internet/mobile providers
- [ ] **Veolia/Aquanet water scraper** — water provider
- [ ] **Two-factor authentication for users** — optional TOTP 2FA in user profile
- [ ] **Backup system** — automated daily DB backup to S3/B2
- [ ] **Monitoring & alerting** — Uptime Kuma or similar for dmstic.com health monitoring
- [ ] **Referral system** — Pro trial for referrals
- [ ] **Data retention policy UI** — user can set auto-delete old data after N months
- [ ] **GDPR compliance** — privacy policy page, cookie consent, data export, account deletion

---

## Completed

- [x] Laravel deployment on VM7000 (NETOL infra)
- [x] MySQL schema: energy_providers, bills, monthly_summary
- [x] TAURON CSV import
- [x] ORLEN CSV import
- [x] Dashboard with charts (Chart.js)
- [x] Analytics: monthly totals, year filter, trends
- [x] Sidebar navigation with provider links
- [x] Provider-specific subpages
- [x] Inline sidebar editing (provider name)
- [x] Theme customization (dark/light/system)
- [x] Forecast tab (linear regression from historical data)
- [x] Document viewer (PDF embed)
- [x] CI/CD: GitHub Actions + self-hosted runner on VM7000
- [x] Private repo credentials handling
- [x] HAProxy 503 fix
- [x] Year filter on overview and provider pages
- [x] Footer simplified to "Dmstic © 2026"

---

*Maintained by Dariusz Porczyński*
