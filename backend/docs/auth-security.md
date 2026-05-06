# Authentication Security Notes

WARNING:
These Super Admin credentials are temporary for development/testing only.
Rotate and replace them immediately before production deployment.

The Super Admin password must be stored only in the server-side `.env` file as `SUPER_ADMIN_PASSWORD`.
Do not commit it, expose it in React, include it in API responses, or store it in browser-accessible code.

Bootstrap command:

```bash
php artisan auth:bootstrap-super-admin
```

Production checklist:

- Set a strong private `SUPER_ADMIN_PASSWORD` in the real deployment environment.
- Run `php artisan migrate --force`.
- Run `php artisan auth:bootstrap-super-admin`.
- Log in once and immediately rotate the initial password.
- Keep GitHub Secret Scanning and Push Protection enabled.
