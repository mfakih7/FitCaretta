# FitCaretta — Laravel + Vite eCommerce Storefront

FitCaretta is a premium sportswear / fashion eCommerce storefront built with Laravel and a Vite-based frontend asset pipeline. It includes an admin panel for managing catalog content and store settings (branding, contact details, and social links).

This README is **project-specific** and documents the **current setup and deployment flow**, including a shared-hosting/cPanel structure where the Laravel app code is outside the domain document root.

---

## Project Overview

- **Backend**: Laravel 10 (PHP 8.1+)
- **Frontend build**: Vite + `laravel-vite-plugin`
- **Database**: MySQL/MariaDB
- **UI**: Bootstrap 5 + project CSS in `resources/css/app.css`
- **Icons**:
  - Inline SVG for some UI controls
  - Font Awesome 6 (loaded via CDN in `resources/views/layouts/frontend.blade.php`)

---

## Requirements

### Local development
- **PHP**: 8.1+ (per `composer.json`)
- **Composer**: 2.x
- **Node.js**: 18+ recommended
- **npm**: bundled with Node
- **MySQL/MariaDB**: MySQL 8+ recommended (MariaDB compatible)

### Production
- **PHP**: 8.1+ with common Laravel extensions (`pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `curl`, `fileinfo`, `gd`/`imagick`)
- **Web server**:
  - Standard Laravel: docroot should be `project/public`
  - cPanel/shared hosting: docroot is typically `public_html` (see dedicated section)

---

## Local Development Setup

### 1) Clone and install dependencies

```bash
git clone <your-repo-url> fitcaretta
cd fitcaretta

composer install
npm install
```

### 2) Environment file

Copy env example and update values:

```bash
copy .env.example .env
php artisan key:generate
```

Configure at least:
- `APP_URL`
- DB settings: `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

> Important: review `.env.example` before using it. It currently contains real-looking mail credentials and should be treated as sensitive if this repo is shared.

### 3) Database migrations + storage link

```bash
php artisan migrate
php artisan storage:link
```

### 4) Frontend assets (Vite)

For local dev with hot reload:

```bash
npm run dev
```

For a production-like build (writes to `public/build`):

```bash
npm run build
```

### 5) Run the app locally

```bash
php artisan serve
```

Then open:
- `http://127.0.0.1:8000`

---

## Frontend Asset Workflow (Vite) — Important Notes

### How Laravel decides between dev server vs built assets

- The Blade directive `@vite(['resources/css/app.css', 'resources/js/app.js'])` is used in `resources/views/layouts/frontend.blade.php`.
- If a `public/hot` file exists, Laravel assumes the Vite dev server is running and will try to load assets from `http://localhost:5173`.
- In production, **`public/hot` must NOT exist**.

### Shared hosting constraint

Your production server (cPanel) does **not** run Node/Vite. Therefore:
- You must run `npm run build` on your local machine (or CI)
- Commit/deploy the generated `public/build` directory (including `manifest.json` and `assets/*`)

---

## Production Deployment (Standard Laravel Server)

Use this when your web server can point the domain document root to `.../public`.

### 1) Provision server and deploy code

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan storage:link
```

### 2) Build assets

Either build on the server (if Node exists) or build locally and upload `public/build`.

```bash
npm ci
npm run build
```

### 3) Optimize caches

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Production Deployment (THIS cPanel / Shared Hosting Setup)

This is your current structure:

- Laravel project (app code) lives at:
  - `/home/fitcbfra/FitCaretta-main`
- Domain document root is:
  - `/home/fitcbfra/public_html`

Because the domain docroot is `public_html`, your goal is:
- Keep Laravel **outside** `public_html` (safe)
- Copy/sync only public-facing files to `public_html`
- Ensure `public_html/index.php` boots the app from `../FitCaretta-main`
- Ensure `.htaccess` exists in `public_html`
- Ensure built Vite assets exist in `public_html/build`

### A) First-time deployment checklist (cPanel)

1) Upload/clone the repo into:
   - `/home/fitcbfra/FitCaretta-main`

2) Create `.env` in `/home/fitcbfra/FitCaretta-main` (do not put it in `public_html`)
   - Set production values (`APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://your-domain`)
   - Configure database credentials for cPanel MySQL

3) Install Composer deps on the server (via SSH if available):

```bash
cd /home/fitcbfra/FitCaretta-main
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan storage:link
```

4) Build assets **locally** and ensure the following exists in your repo/deploy:
   - `/home/fitcbfra/FitCaretta-main/public/build/manifest.json`
   - `/home/fitcbfra/FitCaretta-main/public/build/assets/*`

5) Sync public files into `public_html`:

Copy these from `/home/fitcbfra/FitCaretta-main/public` → `/home/fitcbfra/public_html`:
- `index.php`
- `.htaccess`
- `build/` (entire directory)
- `storage/` symlink (if your hosting allows symlinks) or configure an alternate approach
- any static brand assets under `public/assets/...` and uploaded images under `public/storage/...`

### B) Required `public_html/index.php` wiring

Your `public_html/index.php` must point back to the Laravel app folder:

- `../FitCaretta-main/vendor/autoload.php`
- `../FitCaretta-main/bootstrap/app.php`
- `../FitCaretta-main/storage/framework/maintenance.php`

This mirrors Laravel’s default `public/index.php`, but with paths adjusted to the real location.

### C) `.htaccess` requirement (LiteSpeed/Apache)

If you use LiteSpeed/Apache, make sure `/home/fitcbfra/public_html/.htaccess` exists and matches Laravel’s `public/.htaccess`.

If `.htaccess` is missing or not applied, you may see:
- routes returning server 404 (LiteSpeed 404)
- assets not rewritten/served correctly

### D) Vite assets requirement in cPanel setup

Because the browser requests assets at:
- `/build/assets/...`

Your docroot must contain:
- `/home/fitcbfra/public_html/build/assets/...`
- `/home/fitcbfra/public_html/build/manifest.json`

If `public/build` exists inside the app folder but **not** in `public_html/build`, you’ll get:
- 404 for `/build/assets/...`
- or HTML responses (app page) due to rewrites
- no CSS/JS loaded

---

## Deployment Commands (copy/paste recipes)

### First-time deploy (cPanel)

Run on server:

```bash
cd /home/fitcbfra/FitCaretta-main
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Run locally (then commit/push `public/build`):

```bash
npm install
npm run build
```

Then sync:
- `/home/fitcbfra/FitCaretta-main/public/build` → `/home/fitcbfra/public_html/build`
- `/home/fitcbfra/FitCaretta-main/public/.htaccess` → `/home/fitcbfra/public_html/.htaccess`
- `/home/fitcbfra/FitCaretta-main/public/index.php` → `/home/fitcbfra/public_html/index.php` (with adjusted paths)

### Future deploy (backend/PHP/Blade only)

```bash
cd /home/fitcbfra/FitCaretta-main
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Then sync **only if public files changed**:
- `public/index.php` and `public/.htaccess` to `public_html`

### Future deploy (CSS/JS/Vite changes)

Locally:

```bash
npm install
npm run build
git add public/build
git commit -m "Build assets"
git push
```

On server:

```bash
cd /home/fitcbfra/FitCaretta-main
git pull
php artisan view:clear
php artisan config:cache
```

Then sync:
- `FitCaretta-main/public/build` → `public_html/build`

---

## Image Backfill / Optimization Commands

These Artisan commands exist in this project:

### Product images (main + gallery)

```bash
php artisan fitcaretta:images:backfill --dry-run
php artisan fitcaretta:images:backfill --limit=50
php artisan fitcaretta:images:backfill --force
```

### Category images

```bash
php artisan fitcaretta:categories:images:backfill --dry-run
php artisan fitcaretta:categories:images:backfill --limit=50
php artisan fitcaretta:categories:images:backfill --force
```

### Homepage slide images

```bash
php artisan fitcaretta:homepage-slides:images:backfill --dry-run
php artisan fitcaretta:homepage-slides:images:backfill --limit=50
php artisan fitcaretta:homepage-slides:images:backfill --force
```

### Repair missing simple variants (catalog data fix)

```bash
php artisan fitcaretta:repair-simple-variants --dry-run
php artisan fitcaretta:repair-simple-variants
php artisan fitcaretta:repair-simple-variants --include-inactive-products
```

---

## Cache / Optimization Commands (safe)

Clear caches:

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

Build caches (production):

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Troubleshooting (real issues & fixes)

### 1) Vite assets return 404 / HTML (styles missing)

**Symptoms**
- `/build/assets/...` returns 404 or HTML
- page loads unstyled
- MIME type errors for JS/CSS

**Root cause**
- The web server document root is not pointing to Laravel `public/`, or in cPanel the `build/` folder was not copied into `public_html`.

**Fix**
- Standard server: set docroot to `.../public`
- cPanel: ensure `public_html/build` exists and matches `FitCaretta-main/public/build`

Quick test:
- request `/build/manifest.json` in the browser; it must return JSON.

### 2) Browser tries to load Vite dev server `:5173`

**Symptoms**
- Network requests to `http://::1:5173/...`
- slow page load due to failed dev-server requests

**Root cause**
- `public/hot` exists, so Laravel thinks Vite dev server is running.

**Fix**
- Delete `public/hot` in production and ensure it’s not deployed.

### 3) LiteSpeed/Apache routes return 404

**Symptoms**
- Home works but deeper routes 404
- assets may also behave strangely

**Root cause**
- Missing or incorrect `.htaccess` in docroot.

**Fix**
- Copy Laravel `public/.htaccess` to `public_html/.htaccess` (cPanel) or keep in `public/` (standard server).

### 4) Images not showing after deploy

Common causes:
- `php artisan storage:link` not run
- `public/storage` not present in `public_html` (cPanel copy/symlink issue)
- wrong permissions on `storage/` and `bootstrap/cache/`

---

## Update Workflow (recommended)

### Local → Git → Production (cPanel)

1) Make changes locally
2) If CSS/JS changed: run

```bash
npm run build
```

3) Commit + push
4) On server:

```bash
cd /home/fitcbfra/FitCaretta-main
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

5) Sync public files to docroot:
- `public/index.php` → `public_html/index.php` (preserve your path wiring)
- `public/.htaccess` → `public_html/.htaccess`
- `public/build` → `public_html/build`
- `public/storage` → `public_html/storage` (symlink or copy depending on hosting)

---

## Assumptions (please verify)

- Production hosting is **cPanel/shared hosting** with domain docroot fixed at `/home/fitcbfra/public_html`.
- Node/Vite is **not** available in production; assets are built locally and deployed via Git/FTP.
- Apache/LiteSpeed uses `.htaccess` in `public_html`.

---

## Files inspected to write this README

- `composer.json`
- `package.json`
- `vite.config.js`
- `config/store.php`
- `app/Providers/AppServiceProvider.php` (settings overrides)
- `resources/views/layouts/frontend.blade.php` (`@vite` usage + Font Awesome CDN)
- `public/index.php`
- `.env.example`
- `app/Console/Commands/BackfillImageVariants.php`
- `app/Console/Commands/BackfillCategoryImageVariants.php`
- `app/Console/Commands/BackfillHomepageSlideImageVariants.php`
- `app/Console/Commands/RepairSimpleProductVariants.php`
- `public/.htaccess` (exists) and `deploy/.htaccess` (exists)

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).


🔥 Recommended deploy flow (final)
cd ~/FitCaretta-main
git pull

rsync -av --delete --exclude=index.php ~/FitCaretta-main/public/ ~/public_html/
cp ~/FitCaretta-main/public/.htaccess ~/public_html/.htaccess

php artisan optimize:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear
php artisan cache:clear