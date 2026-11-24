# Deployment Plan — yaramay-website-2026 (Monorepo)

## Server Access

- **SSH command:** `ssh -p 65002 u805024096@178.16.135.22`
- **Port:** 65002
- **User:** u805024096
- **Host:** 178.16.135.22

## Project Layout (Monorepo)

Single repository, single build, single deploy:

```text
yaramay-website-2026/
├── index.html, src/, public/     → Vite marketing site (vanilla JS + Tailwind)
├── invoice-record-system/        → PHP invoice app (admin dashboard, client portal, printable invoices)
│   ├── config.php                → env/URL-driven; see "Server Configuration" below
│   ├── config.local.php          → created ON THE SERVER only (gitignored) for Hostinger MySQL creds
│   └── database.sql              → MySQL schema + seed data (import once)
└── scripts/copy-app.mjs          → copies the PHP app into dist/ during `npm run build`
```

After `npm run build`, `dist/` contains both the static site and the PHP app:

- **Site:** `https://yaramay.online/`
- **Admin dashboard:** `https://yaramay.online/invoice-record-system/login.php`
- **Client portal:** `https://yaramay.online/invoice-record-system/portal.php`
- **Vite base:** `'./'` (relative paths — safe for shared hosting subdirectory deployment)

## Deployment Steps

1. **Build locally** (one command produces everything):
   ```bash
   npm install
   npm run build
   ```
   Output: `dist/` (site + `dist/invoice-record-system/` PHP app; sessions/logs/scratch excluded, runtime dirs recreated empty)

2. **Transfer files to the server**
   ```bash
   scp -P 65002 -r dist/* u805024096@178.16.135.22:domains/yaramay.online/public_html/
   ```
   Or upload `dist.zip` and unzip it directly into `public_html`.

3. **Server configuration (first deploy only)**
   - Copy `config.local.example.php` to `config.local.php` on the server and fill in the Hostinger MySQL credentials (`DB_HOST`/`DB_NAME`/`DB_USER`/`DB_PASS`). `config.php` loads it automatically and never touches git.
   - Import `invoice-record-system/database.sql` into the Hostinger MySQL database once (phpMyAdmin or `mysql` CLI).
   - Ensure `data/sessions/` is writable by PHP (default 0775 usually suffices on Hostinger).

4. **Verify**
   - `https://yaramay.online` — static site loads (relative `./` base avoids path issues)
   - `https://yaramay.online/invoice-record-system/portal.php` — client portal renders
   - `https://yaramay.online/invoice-record-system/login.php` — admin login works
   - Direct access to `*.db` / `*.sql` under `invoice-record-system/` returns 403 (protected by the app's `.htaccess`)

## Notes

- Uploading `dist/*` **adds/overwrites** files on the server but never deletes existing ones — server-side uploads and receipts persist across deploys.
- On the server, the PHP app uses **MySQL** via `config.local.php`. If MySQL is ever unavailable, `config.php` automatically falls back to the tracked `data/invoices.db` SQLite file.
- If SSH key-based auth is preferred, add your local `~/.ssh/id_*.pub` to the server's `~/.ssh/authorized_keys` after first login.
