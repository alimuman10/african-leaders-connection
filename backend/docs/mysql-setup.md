# MySQL Setup and Verification

This backend is MySQL-ready. The earlier blocker was environmental: no MySQL or MariaDB service was reachable on `localhost:3306`, while the Laravel migrations and seeders passed against the configured local database.

## Confirm PHP Support

The local PHP installation should include:

```txt
pdo_mysql
mysqli
mysqlnd
```

Check with:

```powershell
php -m | Select-String -Pattern "pdo|mysql"
```

## Option 1: Native MySQL

1. Install MySQL 8.x or MariaDB.
2. Start the MySQL Windows service.
3. Create a database and user:

```sql
CREATE DATABASE african_leaders_connection CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'alc_user'@'localhost' IDENTIFIED BY 'change_this_password';
GRANT ALL PRIVILEGES ON african_leaders_connection.* TO 'alc_user'@'localhost';
FLUSH PRIVILEGES;
```

4. Copy `backend/.env.mysql.example` to `backend/.env`.
5. Set a strong `DB_PASSWORD` and `ADMIN_PASSWORD`.
6. Run:

```powershell
php artisan key:generate
php artisan migrate:fresh --seed --force
php artisan test
npm run build
```

## Option 2: Docker MySQL

Docker Desktop must be running.

```powershell
docker compose -f docker-compose.mysql.yml up -d
Copy-Item .env.mysql.example .env
php artisan key:generate
php artisan migrate:fresh --seed --force
```

The included Docker profile exposes MySQL on `127.0.0.1:3306` and creates:

```txt
Database: african_leaders_connection
Username: alc_user
Password: change_this_password
```

Change these values before production use.

## Full Verification

Use the project verification script:

```powershell
.\scripts\verify-backend.ps1 -UseMySql -FreshDatabase
```

Without MySQL, verify the current local database:

```powershell
.\scripts\verify-backend.ps1 -FreshDatabase
```

## Production Notes

- Use MySQL with `utf8mb4_unicode_ci`.
- Store credentials only in `.env`.
- Run `php artisan migrate --force` during deployment.
- Use strong database and admin passwords.
- Keep `APP_DEBUG=false` in production.
- Point the web root to `backend/public`.
