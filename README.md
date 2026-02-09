## Library System

### Requirements

- **PHP 8.2+**
- **Composer**
- **Node.js 20+** and npm
- **MySQL or MariaDB** (or another SQL database)

---

## How to run (local development)

### 1. Clone and install dependencies

```bash
cd library-system
composer install
npm install
```

### 2. Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set at least:

- **DB_DATABASE** – database name (create it in MySQL first, e.g. `library_system`)
- **DB_USERNAME** – your MySQL user
- **DB_PASSWORD** – your MySQL password

### 3. Database

```bash
php artisan migrate
php artisan db:seed
```

(Use `php artisan migrate:fresh --seed` to reset and reseed.)

### 4. Build frontend assets

```bash
npm run build
```

(For active development with hot reload use `npm run dev` in a separate terminal.)

### 5. Start the Laravel app

```bash
php artisan serve
```

Open **http://127.0.0.1:8000** in your browser. You can register a new account or use seeded users if any.

### 6. (Optional) Library chatbot

To use the in-app chatbot:

1. In `.env`, set:  
   `CHATBOT_URL=http://localhost:3000`
2. In a **second terminal** run:

```bash
node server.js
```

The chatbot will appear in the bottom-right of the app. If `CHATBOT_URL` is empty or omitted, the chatbot button is hidden.

---

## Free Deployment Options

### Option 1: Render (Recommended - Easiest)

**Free tier:** 750 hours/month (enough for 24/7), 512MB RAM, free SSL

1. **Sign up:** https://render.com (GitHub/Google login)

2. **Create a PostgreSQL database:**
   - Dashboard → New → PostgreSQL
   - Name: `library-db`
   - Plan: Free
   - Copy the **Internal Database URL** (e.g. `postgresql://user:pass@host/dbname`)

3. **Deploy the web service:**
   - Dashboard → New → Web Service
   - Connect your GitHub repo
   - Settings:
     - **Build Command:** `composer install --no-dev --optimize-autoloader && npm install && npm run build && php artisan migrate --force`
     - **Start Command:** `php artisan serve --host=0.0.0.0 --port=$PORT`
     - **Environment:** PHP
   - Add environment variables:
     ```
     APP_ENV=production
     APP_DEBUG=false
     APP_URL=https://your-app.onrender.com
     DB_CONNECTION=pgsql
     DB_HOST=(from database URL)
     DB_PORT=5432
     DB_DATABASE=(from database URL)
     DB_USERNAME=(from database URL)
     DB_PASSWORD=(from database URL)
     ```
   - Plan: Free

4. **Deploy chatbot (optional):**
   - New → Background Worker
   - Build: `npm install`
   - Start: `node server.js`
   - Same DB env vars
   - Set `CHATBOT_URL` in web service to the worker URL

**Note:** Free tier spins down after 15 min inactivity. First request takes ~30s to wake up.

---

### Option 2: Railway

**Free tier:** $5 credit/month (usually enough for small apps)

1. **Sign up:** https://railway.app (GitHub login)

2. **Deploy:**
   - New Project → Deploy from GitHub repo
   - Add PostgreSQL database (auto-configured)
   - Railway detects Laravel and sets up automatically
   - Add environment variables in Variables tab:
     ```
     APP_ENV=production
     APP_DEBUG=false
     ```
   - Generate domain or use provided `.railway.app` domain

3. **Run migrations:**
   - Open project → Deployments → View Logs
   - Or add to build command: `php artisan migrate --force`

**Pros:** Very easy, auto-detects Laravel, includes database

---

### Option 3: Fly.io

**Free tier:** 3 shared VMs, 3GB storage, 160GB outbound transfer

1. **Install Fly CLI:**
   ```bash
   curl -L https://fly.io/install.sh | sh
   ```

2. **Login:**
   ```bash
   fly auth login
   ```

3. **Initialize:**
   ```bash
   fly launch
   ```
   - Choose region
   - Create PostgreSQL database when prompted
   - Don't deploy yet

4. **Create `fly.toml`** (if not auto-generated):
   ```toml
   app = "your-app-name"
   primary_region = "iad"

   [build]
     builder = "paketobuildpacks/builder:base"

   [http_service]
     internal_port = 8000
     force_https = true
     auto_stop_machines = true
     auto_start_machines = true
     min_machines_running = 0
     processes = ["app"]

   [[services]]
     http_checks = []
     internal_port = 8000
     processes = ["app"]
     protocol = "tcp"
     script_checks = []
   ```

5. **Deploy:**
   ```bash
   fly deploy
   ```

6. **Set secrets:**
   ```bash
   fly secrets set APP_KEY=$(php artisan key:generate --show)
   fly secrets set APP_ENV=production
   fly secrets set APP_DEBUG=false
   ```

---

### Option 4: InfinityFree / 000webhost (Limited)

**Free tier:** PHP hosting, MySQL, but limited features

**Limitations:**
- No SSH/command line access
- No Composer/Artisan commands
- Limited to basic PHP hosting
- Not recommended for Laravel (very difficult)

**Better alternative:** Use Render or Railway instead.

---

### Option 5: DigitalOcean App Platform (Free Trial)

**Free trial:** $200 credit for 60 days

1. Sign up: https://www.digitalocean.com
2. Create App → Connect GitHub
3. Auto-detects Laravel
4. Add PostgreSQL database
5. Deploy

After trial, costs ~$5-12/month (cheapest paid option).

---

## Quick Deployment Checklist

Before deploying anywhere:

- [ ] Set `APP_ENV=production` and `APP_DEBUG=false`
- [ ] Generate `APP_KEY` with `php artisan key:generate`
- [ ] Update `APP_URL` to your production domain
- [ ] Configure database credentials
- [ ] Run `composer install --no-dev --optimize-autoloader`
- [ ] Run `npm run build` (not `npm run dev`)
- [ ] Run migrations: `php artisan migrate --force`
- [ ] (Optional) Seed database: `php artisan db:seed --force`
- [ ] Set up queue worker (if using queues)
- [ ] Configure scheduler/cron (for due-date reminders)
- [ ] Set `CHATBOT_URL` if using chatbot

---

## Deployment Guide (Paid/Production)

### 1. Install PHP dependencies

```bash
composer install --no-dev --optimize-autoloader
```

### 2. Install frontend & chatbot dependencies

```bash
npm install
npm run build
```

### 3. Configure environment

Copy the example file and edit it for your environment:

```bash
cp .env.example .env
php artisan key:generate
```

Update at least:

- **APP_URL** – Your public URL (e.g. `https://library.example.com`)
- **APP_DEBUG** – Set to `false` in production
- **DB_*** – Database credentials (same as used by `server.js` for the chatbot)
- **MAIL_*** – For due-date reminders and notifications
- **CHATBOT_URL** (optional) – Full URL of the chatbot API (e.g. `https://chat.example.com`). Leave empty to hide the chatbot widget.

### 4. Run migrations and seeders

```bash
php artisan migrate --force
php artisan db:seed --force
```

### 5. Queue worker and scheduler

- Configure a process manager (e.g. Supervisor, systemd) to run:

```bash
php artisan queue:work --tries=1
```

- Configure a cron entry to run Laravel's scheduler every minute:

```bash
* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
```

This ensures due-date reminders and overdue notifications are processed.

### 6. Start the chatbot server (optional)

To enable the library chatbot:

1. Run the Node server (e.g. with a process manager):

```bash
PORT=3000 node server.js
```

2. Set **CHATBOT_URL** in `.env` to the public URL of this service (e.g. `http://localhost:3000` or `https://chat.yoursite.com`). The frontend will call `CHATBOT_URL/chat` for messages. If **CHATBOT_URL** is empty, the chatbot button is hidden.

### 7. Production checklist

- Set `APP_DEBUG=false` and `APP_ENV=production` in `.env`
- Run `php artisan config:cache` and `php artisan route:cache` after deployment
- Ensure queue worker and scheduler are running (see step 5)
- Use HTTPS and secure session/cookie settings in production

---

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

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
