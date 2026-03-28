# Dmstic — Household Cost Analytics

![Laravel](https://img.shields.io/badge/Laravel-13.2-red) ![PHP](https://img.shields.io/badge/PHP-8.3-blue) ![Chart.js](https://img.shields.io/badge/Chart.js-4.4.0-pink) ![License](https://img.shields.io/badge/License-MIT-green) ![Last Update](https://img.shields.io/badge/Update-2026--03--28-orange)

**Dmstic** is a self-hosted PHP/Laravel web application for tracking, importing, and analysing household utility costs. It aggregates invoices and consumption data from multiple providers (electricity, gas, water, internet, bank) into a unified dashboard with interactive charts, flexible filters, and deep analytics.

**Author**: Dariusz Porczyński
**Live instance**: https://dmstic.netol.com

---

## Features

### Multi-Provider Dashboard
- Unified overview of all utility providers in one place
- Stacked bar chart: monthly costs per provider (last 24 months)
- Doughnut chart: total spend distribution by provider
- Recent bills list across all providers

### Per-Provider Analytics
Three analytics tabs on each provider page:

**Monthly Statistics**
- Table and chart of monthly totals: PLN gross, PLN net, kWh consumed
- Cost per kWh computed per month (useful for tracking tariff changes)

**Year-over-Year Comparison**
- Pivot table: months as rows, years as columns
- See January 2023 vs January 2024 vs January 2025 side by side
- Highlights year-over-year increases and decreases

**Period-to-Period Comparison**
- Compare any two arbitrary date ranges
- Shows total PLN, kWh, cost/kWh for each period
- Calculates absolute and percentage change

### Flexible Filters
- Date range picker (from / to) with quick year-select buttons
- Document type filter with explanations:
  - **FV** — Faktura VAT (standard VAT invoice)
  - **FK** — Faktura Korygująca (correction invoice)
  - **NO** — Nota Odsetkowa (interest note)
  - **NB** — Nota Bankowa (bank note)
- Status filter: paid, unpaid, overdue, cancelled

### Provider Management
- Add unlimited providers (electricity, gas, water, internet, bank, other)
- Edit provider details: name, icon, color, client number, API configuration
- Each provider gets its own analytics page

### Document Management
- Upload PDF invoices manually
- AI-powered parsing via Claude API — extracts amounts, dates, kWh automatically
- View original PDFs inline (document viewer)
- Automatic scraping from supported providers (TAURON, ORLEN)

### Settings & Theming
- App name customisation
- Dark / Light / System theme (follows OS preference)
- Claude API key for AI document parsing
- Color scheme and accent color (planned)

### Sidebar Navigation
- Dynamic provider list with icon and color indicator
- Quick links: Add Provider, Settings, Documentation

---

## Supported Providers

| Provider | Type | Status | Auto-Import |
|----------|------|--------|-------------|
| TAURON | Electricity | Supported | Yes |
| ORLEN / PGNiG | Gas | Supported | Yes |
| Water utility | Water | Planned | Planned |
| Multimedia / ISP | Internet | Planned | Planned |
| Bank | Bank statements | Planned | File import |

---

## Screenshots

> Screenshots will be added in v0.2.

| Dashboard | Provider Page | Settings |
|-----------|--------------|---------|
| _(coming soon)_ | _(coming soon)_ | _(coming soon)_ |

---

## Technology Stack

| Component | Technology |
|-----------|-----------|
| Framework | Laravel 13.2 |
| PHP | 8.3 |
| Database | MySQL 8.x |
| Charts | Chart.js 4.4.0 |
| AI parsing | Claude API (claude-opus-4-5) |
| Frontend | Blade templates + vanilla JS |
| Theme | CSS custom properties (dark/light) |

---

## Self-Hosting Installation Guide

### Requirements

- PHP 8.2+ with extensions: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `json`, `curl`
- Composer 2.x
- MySQL 8.x or MariaDB 10.6+
- Apache or Nginx
- Git

### Step 1: Clone the repository

```bash
git clone https://github.com/Dmstic/dmstic.git /var/www/dmstic
cd /var/www/dmstic
```

### Step 2: Install PHP dependencies

```bash
composer install --no-dev --optimize-autoloader
```

### Step 3: Environment configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your values:

```dotenv
APP_NAME="Dmstic"
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dmstic
DB_USERNAME=dmstic
DB_PASSWORD=your_password
```

### Step 4: Database setup

Create MySQL database and user:

```sql
CREATE DATABASE dmstic CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'dmstic'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON dmstic.* TO 'dmstic'@'localhost';
FLUSH PRIVILEGES;
```

Run migrations:

```bash
php artisan migrate
```

### Step 5: Storage setup

```bash
php artisan storage:link
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Step 6: Web server configuration

**Apache** (enable `mod_rewrite`):

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /var/www/dmstic/public

    <Directory /var/www/dmstic/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**Nginx**:

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/dmstic/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### Step 7: Cache optimisation (production)

```bash
php artisan config:cache
php artisan view:cache
php artisan route:cache
```

### Step 8: Configure settings

Visit `https://yourdomain.com/settings` to set:
- App name
- Color scheme (dark/light/system)
- Claude API key (optional — for AI document parsing)

### Step 9: Add your first provider

Visit `https://yourdomain.com/admin/provider/create` to add a utility provider.

---

## Configuration for TAURON (Electricity, Poland)

If you use TAURON as your electricity provider:

1. Add a provider with type `elec` and scraper class `TauronScraper`
2. Set client number, point number, and service address from your TAURON account
3. Enter your moj.tauron.pl login credentials in the credentials configuration
4. Run the import command:
   ```bash
   php artisan dmstic:import tauron
   ```

---

## Configuration for ORLEN/PGNiG (Gas, Poland)

1. Add a provider with type `gas` and scraper class `OrlenScraper`
2. Set client number and email from your mojakarta.orlen.pl account
3. Enter your credentials
4. Run:
   ```bash
   php artisan dmstic:import orlen
   ```

---

## Updating

```bash
cd /var/www/dmstic
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate
php artisan config:cache
php artisan view:cache
php artisan route:cache
```

---

## Roadmap

See [docs/ROADMAP.md](ROADMAP.md) for the full versioned roadmap.

**Current version**: v0.1 (production)
**In progress**: v0.2 — document viewer, inline editing, CI/CD, forecasting

---

## Contributing

See [docs/CONTRIBUTING.md](CONTRIBUTING.md).

---

## License

MIT License. See `LICENSE` file.

---

**Author**: Dariusz Porczyński
**Created**: 2026-03-28
