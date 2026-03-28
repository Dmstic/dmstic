# Dmstic — Product Roadmap

**Author:** Dariusz Porczyński
**Last updated:** 2026-03-28
**Project:** dmstic — household cost analytics → multi-user SaaS

---

## v0.1 — Foundation ✅ DONE

**Theme:** Core application working with real data

- Laravel deployment on VM7000 (NETOL infrastructure, Apache + MySQL)
- MySQL schema: `energy_providers`, `bills`, `monthly_summary` tables
- TAURON CSV import (energy bills from Tauron Polska Energia)
- ORLEN CSV import (fuel/energy bills from PKN Orlen)
- Dashboard with Chart.js charts: monthly costs, trend lines
- Analytics: monthly totals, per-provider breakdowns
- Filters: year selector
- Responsive UI with sidebar navigation

---

## v0.2 — Developer Workflow & UX ✅ DONE (2026-03-28)

**Theme:** Automation, polish, and extended UI capabilities

- CI/CD pipeline: GitHub Actions workflow
- Self-hosted GitHub Actions runner configured on VM7000
- Automatic redeploy on push to main branch (both private and public repos)
- Private repo credentials management (deploy keys)
- Year filter applied to all pages (overview + per-provider)
- Forecast tab: linear regression projection from historical bill data
- Document viewer: PDF documents embeddable per provider
- Inline sidebar editing: click-to-edit provider name
- Theme customization: dark / light / system preference
- Footer simplified: "Dmstic © 2026" only
- HAProxy 503 fix (stable deployment on NETOL)

---

## v0.3 — Period Filters & Provider Management 🔄 IN PROGRESS

**Theme:** Better data navigation and complete provider lifecycle management

**Target completion:** 2026-04 sprint

- Period filter component at top of all pages (Przegląd + each provider page)
  - Date range picker: from/to
  - Applies to all charts and summary tables
  - Reusable Blade component
- Provider edit panel overhaul:
  - All fields editable: name, type, login, password, API endpoint, account number, notes
  - Delete provider (with confirmation dialog)
  - Delete all bills for a provider (without deleting the provider)
- MPWiK Wrocław scraper: https://ebok.mpwik.wroc.pl/trust/pulpit
  - Automated login and invoice download
  - Parse amounts, dates, m³ consumption
- Main README.md updated:
  - Simple 5-step deployment instructions
  - Technical details moved to docs/README.md
- All documentation migrated to English

---

## v0.4 — Authentication & Multi-Tenancy 📋 PLANNED

**Theme:** Transform from single-user to multi-user foundation

**Dependencies:** v0.3 complete

- Laravel Breeze authentication:
  - Registration: email + password only (maximally simple)
  - Login / logout
  - Email verification required before dashboard access
  - Password reset via email link
- Custom Mailable classes: WelcomeEmail, EmailVerification, PasswordReset
- Gmail SMTP configuration (see docs/GMAIL-SMTP-SETUP.md)
- Multi-tenancy: add `user_id` to all resource tables
  - Global Eloquent scopes for automatic user data isolation
  - Admin bypasses global scopes
  - Data migration: existing data assigned to first user
- File storage isolation: per-user directories
- Basic Filament v3 admin panel:
  - User list with search and filters
  - Edit user: name, email, plan, active status
  - Suspend and delete user actions
- `system_settings` table with encrypted storage
- LLM API key management in admin panel
- `subscription_plan` column on users (free/pro, enforced limits)
- Laravel Queue: database driver, queue worker via Supervisor

---

## v0.5 — AI Parsing & Full SaaS Features 📋 PLANNED

**Theme:** Paid features, AI automation, full admin panel

**Dependencies:** v0.4 complete

- Multi-language (i18n):
  - `lang/en/` and `lang/pl/` with messages, auth, providers files
  - SetLocale middleware (user DB locale + session)
  - Language switcher in header
  - User locale preference in profile
- LLM document parsing pipeline:
  - PDF text extraction (smalot/pdfparser)
  - LLMService: Claude API, OpenAI, or local Ollama
  - DocumentUploadJob: async parsing via queue
  - Parsed data auto-creates bill records
- Local Ollama LLM integration:
  - NETOL infrastructure: http://ollama.netol.io:11434
  - Free parsing for all Pro users when Ollama is configured
- Subscription tier enforcement:
  - Free: 2 providers max, 100 bills max, no AI parsing
  - Pro: unlimited, AI parsing, export, weekly reports
- Full Filament admin panel:
  - Subscription plan management
  - System health dashboard widget
  - Audit log resource (all admin actions logged)
  - Email template management
  - LLM provider/model/key configuration
- TAURON PDF auto-download (background job)
- ORLEN PDF auto-download (background job)
- CSV/Excel export (Pro feature)
- WeeklyReport email (Pro, Sunday 8am scheduled)
- Scraper plug-in architecture (config/scrapers.php, ScraperInterface)

---

## v1.0 — SaaS Launch 🎯 TARGET

**Theme:** Public product, payment processing, scale

**Target:** 2026 Q3/Q4

- Full multi-user SaaS deployed at dmstic.com
  - Domain migration: dmstic.netol.com → dmstic.com
  - Cloudflare DNS + HAProxy config update
  - SSL certificate for dmstic.com
- Public landing page at dmstic.com:
  - Feature overview
  - Pricing (Free / Pro)
  - Signup CTA
  - Screenshots / demo
- Stripe or Paddle payment integration:
  - Self-service Pro plan upgrade
  - Webhooks for plan activation/cancellation
  - Admin can still manually manage plans
- 5+ active provider scrapers: TAURON, ORLEN, MPWiK, + 2 more
- REST API (Laravel Sanctum):
  - API tokens for users
  - GET /api/providers, /api/bills, /api/summary
  - OpenAPI spec (Swagger)
- Mobile PWA:
  - Progressive Web App manifest
  - Service worker for offline support
  - Home screen installable
- Monitoring & alerting:
  - Uptime monitoring for dmstic.com
  - Error tracking (Sentry or Flare)
  - Queue job failure notifications

---

## Post-v1.0 — Growth Features

Items for consideration after successful SaaS launch:

- White-label option (custom branding per account)
- Additional energy providers: ENEA, PGNiG, PSG, Orange, Play
- Two-factor authentication for users (TOTP)
- GDPR compliance tooling: data export, account deletion, privacy dashboard
- Referral system (Pro trial for referrals)
- Team accounts (multiple users per household/organization)
- Mobile native app (React Native or Flutter)
- Data benchmarking: compare your costs vs. regional averages

---

## Version Summary

| Version | Status | Theme |
|---------|--------|-------|
| v0.1 | ✅ Done | Foundation |
| v0.2 | ✅ Done | Developer Workflow & UX |
| v0.3 | 🔄 In Progress | Period Filters & Provider Management |
| v0.4 | 📋 Planned | Authentication & Multi-Tenancy |
| v0.5 | 📋 Planned | AI Parsing & Full SaaS Features |
| v1.0 | 🎯 Target | SaaS Launch at dmstic.com |

---

*Maintained by Dariusz Porczyński*
*Last updated: 2026-03-28*
