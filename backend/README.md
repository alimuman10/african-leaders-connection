# African Leaders Connection Backend

> Leadership. Unity. Progress.

Laravel 12+, Sanctum, React, Vite, Tailwind CSS, and Spatie Laravel Permission power the backend and dashboard foundation for African Leaders Connection.

## Overview

This backend provides a secure API and dashboard layer for a Pan-African leadership platform. It supports authentication, role-based administration, content management, community membership, contact workflows, media uploads, activity logs, and seeded platform data.

## Technology Stack

- Laravel 12+
- PHP 8.2+
- MySQL for production
- Laravel Sanctum for SPA/API authentication
- Laravel Breeze-ready auth foundation
- Spatie Laravel Permission
- React, Vite, Tailwind CSS
- Laravel Mail, queues, scheduler-ready jobs
- Local media storage, Cloudinary-ready environment variables
- Cloudflare Turnstile-ready public form protection

## Authentication

Implemented routes:

```txt
POST /api/register
POST /api/login
POST /api/logout
GET  /api/user
POST /api/forgot-password
POST /api/reset-password
POST /api/email/verification-notification
GET  /api/email/verify/{id}/{hash}
GET  /api/profile
PUT  /api/profile
PUT  /api/password
POST /api/account/deactivate
```

Registration collects full name, email, password, country, phone number, profession, organization, and leadership interest. Passwords use Laravel hashing and strong validation rules. Protected dashboard APIs require authenticated, verified users.

## Roles

Seeded roles:

- Super Admin
- Admin
- Content Manager
- Community Manager
- Member
- Visitor / Guest

Role redirects:

- Super Admin and Admin: `/admin/dashboard`
- Content Manager: `/admin/content`
- Community Manager: `/admin/community`
- Member: `/member/dashboard`

## Admin Features

- Dashboard metrics
- User search and filtering by role, status, and country
- Suspend/reactivate users
- Assign/remove roles
- Stories, services, projects, advocacy, leadership content, settings, media, and contact message management
- Contact archive/reply workflow
- Activity logs for admin actions

## Installation

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan storage:link
php artisan migrate --seed
npm run build
```

For a MySQL local environment, copy `.env.mysql.example` instead of `.env.example` after MySQL is running:

```bash
cp .env.mysql.example .env
php artisan key:generate
php artisan migrate:fresh --seed --force
```

## Environment

Set these values in `.env`:

```env
APP_NAME="African Leaders Connection"
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=african_leaders_connection
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_smtp_user
MAIL_PASSWORD=your_smtp_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@your-domain.com

QUEUE_CONNECTION=database

ADMIN_NAME="African Leaders Connection Admin"
ADMIN_EMAIL=admin@your-domain.com
ADMIN_PASSWORD="Use-A-Strong-Password!"

TURNSTILE_SITE_KEY=
TURNSTILE_SECRET_KEY=

CLOUDINARY_CLOUD_NAME=
CLOUDINARY_API_KEY=
CLOUDINARY_API_SECRET=
```

Never commit `.env`, SMTP credentials, API keys, Cloudinary secrets, or database passwords.

## Verification

Run the local verification suite:

```bash
composer validate
composer install --no-interaction
npm install
php artisan migrate:fresh --seed --force
php artisan route:list --path=api
php artisan test
npm run build
```

PowerShell users can run the included verifier:

```powershell
.\scripts\verify-backend.ps1 -FreshDatabase
```

To require a live MySQL connection during verification:

```powershell
.\scripts\verify-backend.ps1 -UseMySql -FreshDatabase
```

Current local status:

- Composer install: passed
- NPM install: passed, zero vulnerabilities reported
- PHP syntax checks: passed
- API route list: passed
- Migrations and seeders: passed on the configured local test database
- Tests: passed
- Frontend build: passed
- MySQL connection: requires a running MySQL service; local `localhost:3306` was not reachable during verification

See `docs/mysql-setup.md` for native MySQL and Docker MySQL setup.

## Deployment

For Hostinger or VPS:

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Production checklist:

- Point the web root to `backend/public`.
- Use HTTPS.
- Configure MySQL and run migrations with `--force`.
- Configure SMTP mail.
- Configure queue worker or cron-backed queue processing.
- Add Laravel Scheduler to cron.
- Enable Turnstile for public forms.
- Protect `.env` and storage permissions.

## Security Notes

- Sanctum tokens protect API sessions.
- Email verification is required before dashboard access.
- Admin APIs are protected by role middleware.
- Login, registration, password reset, contact, and upload routes are rate limited where appropriate.
- Eloquent is used for database access.
- File uploads validate type and size and use generated storage names.
- Suspended and deactivated users cannot log in.
- Password updates revoke existing API tokens.
- Account deactivation revokes tokens and logs out the session.
