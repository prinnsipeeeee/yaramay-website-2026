# Deployment Guide — YARAMAY Invoice Record System

## Why GitHub Pages Shows `README.md` Instead of the App

**GitHub Pages only hosts static sites (HTML, CSS, JS).** It does **not** run PHP (`index.php`, `login.php`, `portal.php`, `api.php`) or databases (MySQL / SQLite). Because this app is built with PHP + MySQL/SQLite, GitHub Pages falls back to rendering `README.md`.

To run the full system live online (with login sessions, database storage, client portal, and GCash receipt uploads), deploy your repository to a **PHP host** using one of the options below.

> **Tip:** You can turn off GitHub Pages under **GitHub Repo → Settings → Pages → Source: None** so visitors aren't shown the README page.

---

## Option 1: Deploy Directly from GitHub on Render.com (Fastest Free Live Preview)

This repository includes a ready-to-use `Dockerfile` and `render.yaml` Blueprint.

1. Push your latest code to GitHub:
   ```bash
   git add .
   git commit -m "Add PHP hosting & Docker deployment configuration"
   git push origin main
   ```
2. Sign in to [Render.com](https://render.com) with your GitHub account.
3. Click **New + → Blueprint** (or **New + → Web Service**).
4. Select your repository `armyoflight01/invoice-record-sytsem`.
5. Render automatically detects `render.yaml` and `Dockerfile`. Click **Apply / Deploy**.
6. Wait 2–3 minutes for the build to finish. Your live URLs will be:
   - **Admin Login:** `https://<your-app>.onrender.com/login.php`
   - **Client Portal:** `https://<your-app>.onrender.com/portal.php`

> **Important Note on Data Persistence (Render Free Tier):**
> On Render's Free plan, the container filesystem resets when the service sleeps or redeploys (so SQLite changes reset back to the seeded data). For permanent production data on Render, either:
> - Connect a **free cloud MySQL database** (e.g., [Aiven](https://aiven.io) or [TiDB Cloud](https://tidbcloud.com)) and add `DB_DRIVER=mysql`, `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` in **Render → Environment**; OR
> - Upgrade to a Render Disk mounted at `/var/data` and set `DATA_DIR=/var/data`.

---

## Option 2: Deploy Directly from GitHub on Railway (PHP + Managed MySQL)

1. Push your latest code to GitHub.
2. Sign in to [Railway.app](https://railway.app) with GitHub.
3. Click **New Project → Deploy from GitHub repo** and select `invoice-record-sytsem` (Railway auto-detects `Dockerfile`).
4. Inside the same Railway project, click **+ New → Database → Add MySQL**.
5. Open your PHP service → **Variables** and add:
   - `DB_DRIVER` = `mysql`
   - `MYSQL_URL` = `${{MySQL.MYSQL_URL}}` *(reference variable from your MySQL service)*
6. Open your PHP service → **Settings → Networking → Generate Domain**.
7. On first visit, `config.php` automatically connects to MySQL and imports `database.sql` (including tables, default admin `ibraheem01`, and pre-loaded clients/invoices).

---

## Option 3: Free / Shared PHP Hosting (InfinityFree, cPanel, Hostinger)

Traditional PHP + MySQL hosting gives you permanent MySQL storage and file uploads with zero container sleep/reset.

1. Create a MySQL database in your hosting control panel (e.g. InfinityFree Control Panel or cPanel → **MySQL Databases**) and note your:
   - **MySQL Hostname** (e.g. `sqlXXX.infinityfree.com` or `localhost`)
   - **Database Name**
   - **Username**
   - **Password**
2. Upload all project files into your host's `htdocs/` or `public_html/` folder (via File Manager or FTP like FileZilla).
3. Copy `config.local.example.php` to `config.local.php` on the server and enter your MySQL credentials:
   ```php
   <?php
   define('DB_DRIVER', 'mysql');
   define('DB_HOST', 'sqlXXX.infinityfree.com');
   define('DB_PORT', '3306');
   define('DB_NAME', 'if0_XXXXXXXX_yaramay');
   define('DB_USER', 'if0_XXXXXXXX');
   define('DB_PASS', 'YOUR_DATABASE_PASSWORD');
   ```
4. *(Optional)* Open **phpMyAdmin**, select your database, and import `database.sql` (or let `config.php` auto-import it automatically on the first page load).
5. Ensure `uploads/receipts/` and `data/` have write permissions (`755` or `775`).
6. Open your domain:
   - **Admin Dashboard:** `https://yourdomain.com/login.php`
   - **Client Portal:** `https://yourdomain.com/portal.php`

---

## Security Checklist After Deploying Live

- Immediately log in as `ibraheem01` and **change the default password** (`🔑 Password` button in the top bar).
- Verify that visiting `https://yourdomain.com/data/invoices.db` and `https://yourdomain.com/database.sql` returns **403 Forbidden** (protected by `.htaccess`).
- Keep `config.local.php` and `.env` out of Git (already listed in `.gitignore`).
