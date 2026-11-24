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
   - **No database setup needed** — the app runs on SQLite by default, using the pre-seeded `invoice-record-system/data/invoices.db` shipped in `dist/` (contains all clients, invoices, and the admin users).
   - Ensure the PHP process can **write** to `data/invoices.db`, `data/sessions/`, and `uploads/receipts/` on the server (Hostinger: default 0755/0775 usually suffices; bump to 0775 if login fails silently).
   - **Optional MySQL instead:** create `config.local.php` from `config.local.example.php` with `DB_DRIVER = 'mysql'` and the Hostinger MySQL credentials. If MySQL is unreachable the app still auto-falls back to SQLite. `config.local.php` is gitignored so credentials never reach git.

4. **Verify**
   - `https://yaramay.online` — static site loads (relative `./` base avoids path issues)
   - `https://yaramay.online/invoice-record-system/portal.php` — client portal renders
   - `https://yaramay.online/invoice-record-system/login.php` — admin login works
   - Direct access to `*.db` / `*.sql` under `invoice-record-system/` returns 403 (protected by the app's `.htaccess`)

## Notes

- Uploading `dist/*` **adds/overwrites** files on the server but never deletes existing ones — server-side uploads and receipts persist across deploys. Note: overwriting `data/invoices.db` would reset any new records written on the server, so after the first deploy, exclude that file from uploads (or back it up before each deploy).
- **Default database is SQLite** (`data/invoices.db`, pre-seeded, zero-config). MySQL remains available via env vars or `config.local.php` (`DB_DRIVER = 'mysql'`), with automatic SQLite fallback if MySQL is down.
- If SSH key-based auth is preferred, add your local `~/.ssh/id_*.pub` to the server's `~/.ssh/authorized_keys` after first login.
