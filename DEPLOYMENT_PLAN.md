# Deployment Plan — yaramay-website-2026

## Server Access

- **SSH command:** `ssh -p 65002 u805024096@178.16.135.22`
- **Port:** 65002
- **User:** u805024096
- **Host:** 178.16.135.22

## Deployment Target

- **Domain:** yaramay.online
- **Document root:** `yaramay.online/public_html`
- **Remote deploy path:** `/home/u805024096/domains/yaramay.online/public_html/`

## Project

- **Type:** Vite-based frontend (React/Tailwind)
- **Build command:** `npm run build` (outputs to `dist/`)
- **Vite base:** `'./'` (relative paths — safe for shared hosting subdirectory deployment)
- **Prebuilt artifact:** `dist.zip` present in repository root

## Deployment Steps

1. **Build locally**
   ```bash
   npm install
   npm run build
   ```
   Output: `dist/`

2. **Transfer files to the server**
   ```bash
   scp -P 65002 -r dist/* u805024096@178.16.135.22:domains/yaramay.online/public_html/
   ```
   Or upload `dist.zip` and unzip it directly into `public_html`.

3. **Verify**
   - Point a browser at `https://yaramay.online`
   - Confirm assets load (relative `./` base avoids path issues)

## Notes

- `vite.config.js` sets `base: './'`, so the site works from `public_html` without a subpath.
- If SSH key-based auth is preferred, add your local `~/.ssh/id_*.pub` to the server's `~/.ssh/authorized_keys` after first login.
