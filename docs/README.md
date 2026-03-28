# Dmstic Documentation

**Author:** Dariusz Porczyński
**Version:** 0.2 (current) | 1.0 (target SaaS)
**Live URL:** https://dmstic.netol.com
**Future URL:** https://dmstic.com
**Stack:** PHP 8.3, Laravel 13.2, MySQL 8.0, Apache

---

## What is Dmstic?

Dmstic is a household utility cost analytics platform. It aggregates bills and invoices from multiple utility providers (electricity, gas, water, internet), parses them automatically, and presents unified cost analytics with charts, trends, forecasts, and period comparisons.

It is being developed into a multi-user SaaS platform where any household can register, connect their utility accounts, and see all their costs in one place.

---

## Current Features (v0.2)

### Dashboard — Przegląd

The main overview page shows aggregated data across all providers:

- **Total monthly costs** — bar chart showing each month's total spend across all providers
- **Year filter** — switch between years in the top bar
- **Provider breakdown** — cost split by provider per month
- **Trend line** — moving average overlay on cost charts
- **Cost per unit** — shows cost per kWh (electricity), cost per m³ (gas/water), calculated per billing period

### Provider Pages

Each utility provider has a dedicated subpage with:

- **Monthly cost chart** — provider-specific cost history
- **Consumption chart** — units consumed (kWh, m³, etc.) per month
- **Bills table** — list of all imported bills with amounts, dates, periods
- **Analytics panel** — cost statistics: average, min, max, year-over-year change
- **Document viewer** — view original uploaded invoices (PDF embed)
- **Inline editing** — hover over provider name in sidebar to reveal edit button; click for parameter modal

### Forecast Tab

Available on each provider page:

- Linear regression forecast based on historical bill data
- Projects the next 3–6 months of costs
- Visual overlay on the main cost chart

### Sidebar Navigation

- Lists all active providers with icons
- **+ Add Provider** button at bottom
- Hover to reveal inline edit button per provider
- Global settings accessible from sidebar bottom

### Global Settings

- **App name** — customize the app title
- **Color scheme** — dark / light / system
- **Footer text** — customizable footer line
- **LLM API key** — global AI key for automatic document parsing (Claude, OpenAI)

### CI/CD (GitHub Actions)

- Self-hosted runner on VM7000 (NETOL infrastructure)
- Triggers on push to `main` branch of either private or public repo
- Runs: composer install, npm build, artisan migrate, artisan cache:clear
- Deployment target: `/var/www/dmstic` on VM7000

---

## Supported Providers (v0.2)

| Provider | Type | Import Method |
|----------|------|---------------|
| TAURON Polska Energia | Electricity | CSV import |
| ORLEN (PKN Orlen) | Energy/Fuel | CSV import via ebok.myorlen.pl |

---

## Planned Providers (v0.3+)

| Provider | Type | Method |
|----------|------|--------|
| MPWiK Wrocław | Water | Web scraper (ebok.mpwik.wroc.pl) |
| TAURON | Electricity | Auto-download (PDF scraper) |
| ORLEN | Energy | Auto-download (PDF scraper) |
| ENEA | Electricity | Scraper (planned) |
| PGNiG/PSG | Gas | Scraper (planned) |

---

## Architecture

### File Structure

```
dmstic/
├── app/
│   ├── Http/Controllers/   # All page controllers
│   ├── Models/             # Eloquent models
│   ├── Services/           # Business logic (LLMService, SubscriptionService, etc.)
│   ├── Scrapers/           # Provider scraper classes
│   ├── Jobs/               # Queue jobs (document fetch, parsing)
│   └── Mail/               # Mailable classes (Welcome, Verify, Reset, Report)
├── config/
│   └── scrapers.php        # Available scraper registry
├── database/
│   ├── migrations/         # All DB migrations
│   └── seeders/            # Default data seeders
├── lang/
│   ├── en/                 # English translations
│   └── pl/                 # Polish translations
├── resources/views/
│   ├── layouts/            # App layout (header, sidebar, footer)
│   ├── dashboard/          # Overview page views
│   ├── providers/          # Per-provider views
│   ├── emails/             # Email Blade templates
│   └── components/         # Reusable Blade components
├── routes/
│   ├── web.php             # Web routes
│   └── console.php         # Scheduled tasks
├── docs/                   # This documentation
└── .github/
    └── workflows/          # CI/CD GitHub Actions
```

### Database Schema

**`users`**
```
id, name, email, password, email_verified_at, locale, subscription_plan, is_admin,
remember_token, created_at, updated_at
```

**`energy_providers`**
```
id, user_id, name, type, scraper_class, login(enc), password(enc), api_endpoint,
account_number, notes, active, last_fetched_at, created_at, updated_at
```

**`bills`**
```
id, user_id, provider_id, document_id, billing_period_start, billing_period_end,
amount_due, energy_consumed, unit, parsed_at, created_at, updated_at
```

**`monthly_summary`**
```
id, user_id, provider_id, year, month, total_cost, total_consumed, unit,
cost_per_unit, created_at, updated_at
```

**`documents`**
```
id, user_id, provider_id, filename, storage_path, mime_type, parsed_at,
created_at, updated_at
```

**`system_settings`**
```
id, key, value(enc), created_at, updated_at
```

---

## SaaS Roadmap Summary

### v0.3 (In Progress)
Period filter on all pages, provider full edit/delete, MPWiK scraper, English docs

### v0.4 (Planned)
Laravel Breeze auth, multi-tenancy (user_id), Gmail SMTP, Filament admin panel

### v0.5 (Planned)
Multi-language (en/pl), LLM parsing, Ollama integration, subscription tiers

### v1.0 (Target SaaS Launch)
Full multi-user SaaS at dmstic.com, Stripe billing, public landing page, REST API

Full roadmap: [ROADMAP.md](ROADMAP.md)

---

## Multi-Language Support

Translations live in `lang/{locale}/` files:

```
lang/
├── en/
│   ├── auth.php       # Login, register, verify, reset strings
│   ├── messages.php   # General UI strings
│   └── providers.php  # Provider domain terms (kWh, m³, billing period)
└── pl/
    ├── auth.php
    ├── messages.php
    └── providers.php
```

All Blade templates use `{{ __('messages.key') }}` syntax. Never hardcode display strings in templates.

The language switcher in the header allows users to toggle between EN and PL. Preference is stored in the `users.locale` column and in session for guests.

---

## Authentication Flow

1. User registers at `/register` — only email + password required
2. App generates name from email prefix
3. WelcomeEmail sent immediately (queued)
4. EmailVerification sent immediately (queued)
5. User must verify email before accessing dashboard
6. After verification: redirected to `/dashboard`

Password reset: `/forgot-password` → email link → `/reset-password/{token}` → new password.

---

## Admin Panel (Filament v3)

Admin panel lives at `/admin`. Only users with `is_admin = true` can access it.

Features:
- **User management:** list, edit, suspend, delete, change subscription plan
- **System settings:** LLM provider, API key, model selection
- **LLM config:** Claude / OpenAI / Ollama selection, endpoint configuration
- **Audit log:** all admin actions logged with before/after values
- **System health widget:** user stats, queue size, email quota
- **Email template management:** customize transactional email content

---

## LLM Document Parsing

When a user uploads a PDF invoice or the scraper downloads one:

1. Queue job dispatched: `DocumentUploadJob`
2. PDF text extracted via `smalot/pdfparser`
3. Text sent to configured LLM (Claude, OpenAI, or local Ollama)
4. LLM returns JSON: billing_period, amount_due, energy_consumed, due_date, etc.
5. Bill record created in database
6. User sees parsed data on their provider page

**Local LLM option:** Ollama running on NETOL infrastructure at `http://ollama.netol.io:11434`. Zero external API cost.

---

## Security Model

| Area | Mechanism |
|------|-----------|
| Passwords | bcrypt via Laravel Hash |
| Provider credentials | Laravel encrypt() per row |
| LLM API keys | Encrypted in system_settings |
| User files | Private storage, authenticated download URL |
| CSRF | Laravel middleware (all POST/PUT/DELETE) |
| SQL injection | Eloquent parameterized queries |
| Session | Signed, HTTPS only |
| Email verification | Required before dashboard access |
| Admin access | is_admin gate + separate Filament login |

---

## Email Configuration

Gmail SMTP is used as the transactional email hub.

Full setup instructions: [GMAIL-SMTP-SETUP.md](GMAIL-SMTP-SETUP.md)

Mailable classes:
- `WelcomeEmail` — on registration
- `EmailVerification` — verify email address
- `PasswordReset` — password reset link
- `WeeklyReport` — optional Pro weekly summary (Sunday 8am)

---

## Subscription Plans

| Feature | Free | Pro |
|---------|------|-----|
| Providers | 2 max | Unlimited |
| Bills stored | 100 max | Unlimited |
| AI document parsing | No | Yes |
| Auto-fetch (scrapers) | No | Yes |
| Export CSV/Excel | No | Yes |
| Weekly email report | No | Yes |
| Local Ollama LLM | No | Yes |

Plan is stored in `users.subscription_plan`. Admin can change manually in Filament. Future: Stripe/Paddle self-service upgrade.

---

## Deployment

For deployment instructions, see the main project [README.md](../README.md).

---

## Related Documents

| File | Description |
|------|-------------|
| [ROADMAP.md](ROADMAP.md) | Version-by-version feature roadmap |
| [TODO.md](TODO.md) | Prioritized task list |
| [GMAIL-SMTP-SETUP.md](GMAIL-SMTP-SETUP.md) | Gmail SMTP configuration guide |
| [architect/SAAS-ARCHITECTURE.md](../architect/SAAS-ARCHITECTURE.md) | Full SaaS architecture specification |
| [architect/2026-03-28-inicjalizacja-planowanie-projektu-dmstic.md](../architect/2026-03-28-inicjalizacja-planowanie-projektu-dmstic.md) | Verbatim archive of founding prompts |

---

*Maintained by Dariusz Porczyński*
*Last updated: 2026-03-28*
