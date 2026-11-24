# YARAMAY Computer Maintenance Services - Invoice Record System

Official invoice record, client management, and billing system built for **YARAMAY Computer Maintenance Services**. Featuring bilingual English & Arabic support, prominent price and remaining balance tracking, an interactive client invoice request portal, and an Admin Dashboard with full client information dossier.

---

## 🌟 Key Features

### 1. Pre-loaded Clients & Invoices (From Official Records)
The database is pre-seeded with all verified clients and invoices from the official documents:
- **Brilliant Minds Inc.** (Ms. Pilar R. Corporal) – Midland Plaza Adriatico Manila
  - Invoice #: `00364` | Date: `September 30, 2026`
  - Total Cost / Price: **₱90,000.00**
  - Amount Paid: **₱15,000.00** (5th payment for monthly ₱7,500)
  - Remaining Balance: **₱37,500.00** (Partial Payment)
- **The Jobconnection Manpower** (Mr. Allan Rom) – Ped Gil, Manila
  - Invoice #: `00372` | Date: `October 30, 2026`
  - Total Cost / Price: **₱7,500.00**
  - Amount Paid: **₱7,500.00**
  - Remaining Balance: **₱0.00** (Paid in Full)
- **Simsoh Agency** (Mr. Allan Rom) – Manila
  - Invoice #: `00355` | Date: `October 26, 2026`
  - Total Cost / Price: **₱5,000.00**
  - Amount Paid: **₱5,000.00**
  - Remaining Balance: **₱0.00** (Paid in Full)
- **Joseline International Manpower Corporation** (Mr. Elmer Aquino) – Brgy, Bakal Makati City
  - Invoice #: `0369` | Date: `October 30, 2026`
  - Total Cost / Price: **₱5,500.00**
  - Amount Paid: **₱5,500.00**
  - Remaining Balance: **₱0.00** (Paid in Full)

---

### 2. Admin Dashboard with Full Client Information & Remaining Balances
- **Financial KPIs**:
  - Total Billed / Sales: **₱108,000.00**
  - Total Payments Collected: **₱33,000.00**
  - Outstanding Remaining Balance: **₱37,500.00**
  - Total Registered Clients: **4**
- **Client Information Overview Table**:
  - Displays each client's Company Name, Contact Person, Invoices Count, Total Billed, Total Paid, and Remaining Balance.
  - One-click **"Client Dossier"** modal to view all information and invoice history for any client.
  - One-click **"+ New Invoice"** button for that client.
- **Recent Invoices Table**:
  - Displays Invoice #, Client Name, Issue Date, Price, Paid, Balance, and Status Badge with quick actions.

---

### 3. Price & Remaining Balance Tracking
- Every invoice clearly tracks:
  - **Total Cost / Price (₱)**
  - **Amount Paid (₱)**
  - **Remaining Balance (₱)**: Highlighted with green badge for Paid (`✓ 0.00 Paid`) or red/amber badge for Balance Due (`₱ XX,XXX.XX Due`).
- **Quick Record Payment Button (`💳 Record Payment`)**:
  - Admin can quickly collect and record a payment on any invoice, which automatically updates the Paid Amount and recalculates the Remaining Balance in real-time.

---

### 4. Separate Dedicated Client Invoice Portal (`portal.php`)
- Dedicated standalone portal URL (`portal.php`):
  - Completely separated from the admin dashboard so external clients do not see admin tools or financial summaries.
  - Allows clients to fill up company info, address, service package, and overall total price.
  - **Live Installment Plan Formula**:
    - **Full Payment**: 1x upfront.
    - **3 Months Installment**: Divides total price by 3 with live calculation (e.g. `₱20,000 ÷ 3 = ₱6,666.67 / month`).
    - **6 Months Installment**: Divides total price by 6 with live calculation (e.g. `₱20,000 ÷ 6 = ₱3,333.33 / month`).
  - **Payment Channel**: GCash (**Ferdenand Sabado - 09617190588**) or Cash.
  - **Partner Assignment**: Choose Mr. Ibraheem, Mr. Fawas, or Both (50/50 split).
  - Instant submission receipt with direct printable invoice link.

---

### 5. Secure Admin Authentication & User Management
- **Protected Admin Dashboard (`index.php`)**:
  - Secure login portal at `login.php`.
  - Default Superadmin Account:
    - **Username**: `ibraheem01`
    - **Password**: `123456789`
- **Admin Users Access Control (`view-users`)**:
  - Admins can view all administrator accounts.
  - **Add New Admin**: Create additional admin or superadmin users with custom usernames, passwords, and permissions.
  - **Edit Admin**: Edit usernames (including editing `ibraheem01` username), full names, and roles.
  - **Change Password**: Change passwords for any admin or use the quick "🔑 Password" button in the topbar to update personal credentials.
  - **Root Protection**: System prevents deleting the last active Superadmin account.
  - **Logout**: Safe session termination via `logout.php`.

---

### 5. Official YARAMAY Printable Invoice (`print.php`)
- Matches the authentic company layout from the uploaded invoices:
  - YARAMAY Red & Blue Emblem Logo
  - Company Address: `#19 Street Brgy. San Andres Cainta Rizal Cambridge C5 -1G`
  - Contact Numbers: `+639075251376 (SMS) / +966508624264`
  - Website: `www.yaramay.online`
  - Bill To section (Name, Company, Address)
  - 10-point package inclusions breakdown
  - Summary table: Total Cost, Payment, and Balance
  - Suggestions box with STC Pay / GCash info (`09617190588`)
  - Sign-off by General Manager **Ferdenand (Ibraheem) Sabado** (MCP, MCSE, MCSA and CISCO Certified)
  - Optimized for standard A4 printing and PDF export.

---

### 6. Advanced Security Shield, WAF & Threat Protection (`security.php`)
The system includes enterprise-grade defensive security to prevent hacker attacks, malicious code execution, virus uploads, and automated bots:
- **Real-Time Web Application Firewall (WAF)**:
  - Continuously inspects `$_GET`, `$_POST`, `$_COOKIE`, and raw JSON request bodies.
  - Intercepts and blocks **SQL Injection (SQLi)** (`UNION SELECT`, `SLEEP()`, `BENCHMARK()`, `' OR 1=1--`, etc.).
  - Intercepts and blocks **Cross-Site Scripting (XSS)** (`<script>`, `javascript:`, event handlers).
  - Intercepts and blocks **Remote Code Execution (RCE) & Command Injection** (`system()`, `eval()`, `passthru()`, `powershell`, `cmd.exe`, backticks).
  - Intercepts and blocks **Directory Traversal** (`../`, `..\`, `%2e%2e`) and **Null Byte Injection**.
  - Offending requests are blocked with **HTTP 403 Forbidden** and permanently logged with IP and payload.
- **Anti-Brute-Force & IP Lockout Protection**:
  - Automatically tracks failed login attempts.
  - Locks out any IP or account after **5 failed attempts within 15 minutes** with **HTTP 429 Too Many Requests**.
  - Clears counters upon successful authentication.
- **Anti-Malware & Webshell File Upload Scanner**:
  - Deep-scans all uploaded backup files (`.json`) for embedded PHP shells, `eval()`, `base64_decode()`, polyglot scripts, or executable code.
  - Rejects malicious uploads before any server execution.
- **Database & Directory Direct-Access Blocking**:
  - Dedicated `.htaccess` and `web.config` rules deny direct HTTP downloading of `invoices.db`, `.sql` dumps, and session files.
  - Directory indexing (`Options -Indexes`) is disabled.
- **Cryptographic CSRF Shield**:
  - High-entropy 64-character tokens (`hash_equals`) protect state-changing endpoints from Cross-Site Request Forgery.
- **Strict Security HTTP Headers**:
  - `X-Frame-Options: SAMEORIGIN` (Clickjacking defense)
  - `X-Content-Type-Options: nosniff` (MIME-sniffing defense)
  - `X-XSS-Protection: 1; mode=block`
  - `Referrer-Policy: strict-origin-when-cross-origin`
  - `Content-Security-Policy` (CSP restricting unauthorized scripts, styles, objects)
- **Live Security Incident & Audit Log Dashboard (`Security Shield & Logs`)**:
  - Admin can view real-time security events, blocked attacks, failed logins, and active lockouts directly in the dashboard.

---

### 7. 100% Bilingual English & Arabic Translation
- **Default Language**: English (`en`).
- **One-Click Switcher**: Toggle effortlessly between English (LTR) and Arabic (RTL) via the top bar button.
- User preference is saved across sessions in `localStorage`.

---

## 🚀 How to Run in XAMPP
1. Place the folder in `C:\xampp\htdocs\invoice-record-system`.
2. Start Apache in XAMPP Control Panel.
3. Open your browser and navigate to:
   - **Application**: `http://localhost/invoice-record-system/`
   - **Print Invoice**: `http://localhost/invoice-record-system/print.php?id=1`
   - **Client Portal**: `http://localhost/invoice-record-system/index.php` (Click on "Client Portal" in topbar or navigation)
