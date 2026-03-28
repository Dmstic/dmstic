# Dmstic

![Version](https://img.shields.io/badge/Version-0.2.0-blue) ![PHP](https://img.shields.io/badge/PHP-8.3-purple) ![Laravel](https://img.shields.io/badge/Laravel-13-red) ![Update](https://img.shields.io/badge/Update-2026--03--28-orange)

Household cost analytics — track electricity, gas, water, and other utility bills in one place.

## Quick Deploy

```bash
# 1. Clone
git clone https://github.com/Dmstic/dmstic.git
cd dmstic

# 2. Install dependencies
composer install --no-dev --optimize-autoloader

# 3. Configure
cp .env.example .env
php artisan key:generate
# Edit .env: DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 4. Database
php artisan migrate

# 5. Storage link
php artisan storage:link

# 6. Cache (production)
php artisan config:cache && php artisan route:cache && php artisan view:cache

# 7. Web server
# DocumentRoot: /path/to/dmstic/public
# PHP 8.3+, MySQL 8+
```

## Requirements

- PHP 8.3+ with extensions: pdo_mysql, mbstring, openssl, fileinfo
- MySQL 8.0+
- Composer 2+

## Features

- Multi-provider dashboard (electricity, gas, water, internet, bank)
- Year/date range period filter on every page
- Monthly cost charts, year-over-year comparison, linear forecast
- Document viewer (PDF inline, image preview)
- Dark/light theme with accent color customization
- GitHub Actions CI/CD with self-hosted runner

## Documentation

See [docs/README.md](docs/README.md) for full architecture, data sources, and API reference.

## License

MIT — Author: Dariusz Porczyński
