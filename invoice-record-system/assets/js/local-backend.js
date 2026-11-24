/**
 * YARAMAY Computer Maintenance Services
 * Static / GitHub Pages Local Backend Engine (localStorage-backed API emulator)
 *
 * Activated ONLY on static HTML pages (index.html, login.html, portal.html, print.html).
 * Intercepts fetch('api.php...') calls and serves the complete Invoice Record System API
 * with persistent browser storage so the full frontend & backend run seamlessly on GitHub Pages,
 * while XAMPP / PHP servers continue using the native PHP + MySQL/SQLite backend.
 */

(function () {
    'use strict';

    window.YARAMAY_STATIC_MODE = true;

    const DB_KEY = 'yaramay_invoice_db_v27';
    const SESSION_KEY = 'yaramay_admin_session_v27';
    const LOGOUT_FLAG_KEY = 'yaramay_explicit_logout_v27';

    function nowSql() {
        const d = new Date();
        const pad = (n) => String(n).padStart(2, '0');
        return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
    }

    function todayDate(offsetDays = 0) {
        const d = new Date();
        if (offsetDays) d.setDate(d.getDate() + offsetDays);
        const pad = (n) => String(n).padStart(2, '0');
        return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
    }

    function round2(n) {
        return Math.round((parseFloat(n) || 0) * 100) / 100;
    }

    function fmtNum(n) {
        return (parseFloat(n) || 0).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function computeShares(partner, paid) {
        const p = round2(paid);
        if (partner === 'ibraheem') return { ibraheem: p, fawas: 0 };
        if (partner === 'fawas') return { ibraheem: 0, fawas: p };
        const half = round2(p / 2);
        return { ibraheem: half, fawas: half };
    }

    function getSeedData() {
        const ts = '2026-09-30 09:00:00';
        return {
            settings: {
                company_name: 'YARAMAY Computer Maintenance Services',
                company_cr: 'Cambridge C5 -1G',
                company_vat: 'VAT-PH-09617190588',
                company_phone: '+639075251376 (SMS) / +966508624264',
                company_email: 'billing@yaramay.online',
                company_address: '#19 Street Brgy. San Andres Cainta Rizal Cambridge C5 -1G',
                company_manager: 'Ferdenand (Ibraheem) Sabado',
                company_title: 'General Manager',
                company_cert: 'MCP, MCSE, MCSA and CISCO Certified',
                company_website: 'www.yaramay.online',
                company_gcash: '09617190588',
                gcash_account_name: 'Ferdenand Sabado',
                partner_1_name: 'Mr. Ibraheem',
                partner_2_name: 'Mr. Fawas',
                company_stc: 'STC Pay / GCash',
                company_logo: 'assets/images/yaramay-logo.png',
                currency: 'PHP',
                currency_symbol: '₱',
                default_tax_rate: '0',
                invoice_prefix: 'INV-',
                invoice_footer: "We are open for your suggestions on improving our services with you.\nPayment can be transferred through STC Pay or GCash (Account Name: Ferdenand Sabado, GCash #: 09617190588).\nThanking you for doing Business with us!"
            },
            users: [
                {
                    id: 1,
                    username: 'ibraheem01',
                    password: '123456789',
                    full_name: 'Mr. Ibraheem (Superadmin)',
                    role: 'superadmin',
                    email: 'ibraheem@yaramay.online',
                    created_at: ts,
                    updated_at: ts
                }
            ],
            clients: [
                {
                    id: 1,
                    name: 'Brilliant Minds Inc.',
                    contact_person: 'Ms. Pilar R. Corporal',
                    phone: '+639075251376',
                    email: 'contact@brilliantminds.ph',
                    address: 'Midland Plaza Adriatico Manila',
                    tax_number: 'TIN-BM-00364',
                    created_at: ts
                },
                {
                    id: 2,
                    name: 'The Jobconnection Manpower',
                    contact_person: 'Mr. Allan Rom',
                    phone: '+639075251376',
                    email: 'allan.rom@jobconnection.com.ph',
                    address: 'Ped Gil . Manila',
                    tax_number: 'TIN-JC-00372',
                    created_at: ts
                },
                {
                    id: 3,
                    name: 'Simsoh Agency',
                    contact_person: 'Mr. Allan Rom',
                    phone: '+639075251376',
                    email: 'info@simsoh-agency.com',
                    address: 'Manila',
                    tax_number: 'TIN-SA-00355',
                    created_at: ts
                },
                {
                    id: 4,
                    name: 'Joseline International Manpower Corporation',
                    contact_person: 'Mr. Elmer Aquino',
                    phone: '+639075251376',
                    email: 'elmer.aquino@joseline-manpower.ph',
                    address: 'Brgy, Bakal Makati City',
                    tax_number: 'TIN-JI-00369',
                    created_at: ts
                }
            ],
            products: [
                {
                    id: 1,
                    name: 'Package of E-Commerce Maintenance Services and Web maintenance including Rescue System',
                    price: 7500.0,
                    tax_rate: 0.0,
                    description: 'Includes 10-point maintenance package: Web design/system, Job post/offer, Deployment system (QMS), Maintenance system, Location rescue, SSL security, PRA deployment, FRA/SRA system, OFW block listing, Voucher system, Hosting and domain',
                    created_at: ts
                },
                {
                    id: 2,
                    name: 'Package of E-Commerce Maintenance & Web Maintenance (Annual Contract)',
                    price: 90000.0,
                    tax_rate: 0.0,
                    description: 'Full annual enterprise coverage with monthly payment option (PHP 7,500/month or PHP 90,000/year)',
                    created_at: ts
                },
                {
                    id: 3,
                    name: 'Web and System (Online) & Maintenance Services',
                    price: 5000.0,
                    tax_rate: 0.0,
                    description: 'Monthly online system support, server monitoring, bug fixes, and continuous maintenance',
                    created_at: ts
                },
                {
                    id: 4,
                    name: 'Hardware & Server Maintenance Technical Support',
                    price: 5500.0,
                    tax_rate: 0.0,
                    description: 'Hardware maintenance, company email server maintenance, and remote rescue services',
                    created_at: ts
                }
            ],
            invoices: [
                {
                    id: 1,
                    invoice_number: '00364',
                    client_id: 1,
                    issue_date: '2026-09-30',
                    due_date: '2026-10-30',
                    status: 'partial',
                    payment_method: 'gcash',
                    payment_term: 'custom',
                    installment_months: 12,
                    monthly_payment: 7500.0,
                    partner_assigned: 'both',
                    ibraheem_share: 7500.0,
                    fawas_share: 7500.0,
                    subtotal: 90000.0,
                    discount: 0.0,
                    tax_amount: 0.0,
                    grand_total: 90000.0,
                    paid_amount: 15000.0,
                    remaining_balance: 37500.0,
                    price_note: 'Php 90,000 Per Year Or PHP 7,500 Per Month',
                    amount_due_note: '15,000 php partial payment (5th payment for monthly php 7,500)',
                    package_includes: '["1. Web design / Web system","2. Job post / Job Offer","3 Deployment System","4. Company E-mail Server Maintenance","5. Location Rescue system","6. E-commerce site with SSL Security","7. PRA Deployment","8. FRA/SRA SYSTEM (IF NEEDED)","9. Block Listing for OFW","10. Voucher System","Hosting and domain, with SSL security","--- Nothing Follows ---"]',
                    notes: 'We are open for your suggestions on improving our services with you.\nCan the payment be transferred through STC Pay or Gcash and Gcash # + 09617190588\nThanking you for doing Business with us!',
                    payment_receipt: null,
                    submitted_by: 'admin',
                    created_at: ts
                },
                {
                    id: 2,
                    invoice_number: '00372',
                    client_id: 2,
                    issue_date: '2026-10-30',
                    due_date: '2026-11-15',
                    status: 'paid',
                    payment_method: 'gcash',
                    payment_term: 'full',
                    installment_months: 1,
                    monthly_payment: 7500.0,
                    partner_assigned: 'both',
                    ibraheem_share: 3750.0,
                    fawas_share: 3750.0,
                    subtotal: 7500.0,
                    discount: 0.0,
                    tax_amount: 0.0,
                    grand_total: 7500.0,
                    paid_amount: 7500.0,
                    remaining_balance: 0.0,
                    price_note: 'PHP 7,500 Per Month',
                    amount_due_note: '7,500 PHP',
                    package_includes: '["1. Web design / Web system","2. Job post / Job Offer","3 Deployment System (QMS)","4. Maintenance System","5. Location Rescue system","6. E-commerce site with SSL Security","7. PRA Deployment","8. FRA/SRA SYSTEM (IF NEEDED)","9. Block Listing for OFW","10. Voucher System","Hosting and domain, with SSL security","--- Nothing Follows ---"]',
                    notes: 'We are open for your suggestions on improving our services with you.\nCan the payment be transferred through STC Pay or Gcash and Gcash # 09617190588\nThanking you for doing Business with us!',
                    payment_receipt: null,
                    submitted_by: 'admin',
                    created_at: ts
                },
                {
                    id: 3,
                    invoice_number: '00355',
                    client_id: 3,
                    issue_date: '2026-10-26',
                    due_date: '2026-11-10',
                    status: 'paid',
                    payment_method: 'gcash',
                    payment_term: 'full',
                    installment_months: 1,
                    monthly_payment: 5000.0,
                    partner_assigned: 'ibraheem',
                    ibraheem_share: 5000.0,
                    fawas_share: 0.0,
                    subtotal: 5000.0,
                    discount: 0.0,
                    tax_amount: 0.0,
                    grand_total: 5000.0,
                    paid_amount: 5000.0,
                    remaining_balance: 0.0,
                    price_note: 'Php 5,000',
                    amount_due_note: 'Php 5,000',
                    package_includes: '["Web and System (Online) & Maintenance Services for the Month of Oct"]',
                    notes: 'We are open for your suggestions on improving our services with you.\nThanking you for doing Business with us!',
                    payment_receipt: null,
                    submitted_by: 'admin',
                    created_at: ts
                },
                {
                    id: 4,
                    invoice_number: '0369',
                    client_id: 4,
                    issue_date: '2026-10-30',
                    due_date: '2026-11-15',
                    status: 'paid',
                    payment_method: 'gcash',
                    payment_term: 'full',
                    installment_months: 1,
                    monthly_payment: 5500.0,
                    partner_assigned: 'both',
                    ibraheem_share: 2750.0,
                    fawas_share: 2750.0,
                    subtotal: 5500.0,
                    discount: 0.0,
                    tax_amount: 0.0,
                    grand_total: 5500.0,
                    paid_amount: 5500.0,
                    remaining_balance: 0.0,
                    price_note: 'Maintenance',
                    amount_due_note: 'Maintenance',
                    package_includes: '["1. Web design / Web system","2. Job post / Job Offer","3. Technical Support (Hardware Maintenance)","4. Company E-mail Server Maintenance","5. Location Rescue system","6. E-commerce site with SSL Security","7. PRA Deployment","8. FRA/SRA SYSTEM (IF NEEDED)","9. Company Email Maintenance","10. Voucher System","Hosting and domain, with SSL security","--- Nothing Follows ---"]',
                    notes: 'We are open for your suggestions on improving our services with you.\nCan the payment be transferred through Gcash 09617190588\nThanking you for doing Business with us!',
                    payment_receipt: null,
                    submitted_by: 'admin',
                    created_at: ts
                }
            ],
            invoice_items: [
                {
                    id: 1,
                    invoice_id: 1,
                    item_name: 'Package of E-Commerce Maintenance Services and Web maintenance including Rescue System',
                    description: 'Includes: 1. Web design / Web system\n2. Job post / Job Offer\n3 Deployment System\n4. Company E-mail Server Maintenance\n5. Location Rescue system\n6. E-commerce site with SSL Security\n7. PRA Deployment\n8. FRA/SRA SYSTEM (IF NEEDED)\n9. Block Listing for OFW\n10. Voucher System\nHosting and domain, with SSL security\n--- Nothing Follows ---',
                    quantity: 1,
                    unit_price: 90000.0,
                    discount: 0.0,
                    tax_rate: 0.0,
                    total: 90000.0
                },
                {
                    id: 2,
                    invoice_id: 2,
                    item_name: 'Package of E-Commerce Maintenance Services and Web maintenance including Rescue System',
                    description: 'Includes: 1. Web design / Web system\n2. Job post / Job Offer\n3 Deployment System (QMS)\n4. Maintenance System\n5. Location Rescue system\n6. E-commerce site with SSL Security\n7. PRA Deployment\n8. FRA/SRA SYSTEM (IF NEEDED)\n9. Block Listing for OFW\n10. Voucher System\nHosting and domain, with SSL security\n--- Nothing Follows ---',
                    quantity: 1,
                    unit_price: 7500.0,
                    discount: 0.0,
                    tax_rate: 0.0,
                    total: 7500.0
                },
                {
                    id: 3,
                    invoice_id: 3,
                    item_name: 'Web and System (Online) & Maintenance Services for the Month of Oct',
                    description: 'Web and System (Online) & Maintenance Services for the Month of Oct',
                    quantity: 1,
                    unit_price: 5000.0,
                    discount: 0.0,
                    tax_rate: 0.0,
                    total: 5000.0
                },
                {
                    id: 4,
                    invoice_id: 4,
                    item_name: 'Package of E-Commerce Maintenance Services and Web maintenance including Rescue System',
                    description: 'Includes: 1. Web design / Web system\n2. Job post / Job Offer\n3. Technical Support (Hardware Maintenance)\n4. Company E-mail Server Maintenance\n5. Location Rescue system\n6. E-commerce site with SSL Security\n7. PRA Deployment\n8. FRA/SRA SYSTEM (IF NEEDED)\n9. Company Email Maintenance\n10. Voucher System\nHosting and domain, with SSL security\n--- Nothing Follows ---',
                    quantity: 1,
                    unit_price: 5500.0,
                    discount: 0.0,
                    tax_rate: 0.0,
                    total: 5500.0
                }
            ],
            security_logs: [
                {
                    id: 1,
                    event_type: 'LOGIN_SUCCESS',
                    ip_address: '127.0.0.1',
                    username: 'ibraheem01',
                    details: 'YARAMAY Security Shield active — Admin session initialized',
                    user_agent: navigator.userAgent.substring(0, 120),
                    created_at: nowSql()
                }
            ],
            login_attempts: []
        };
    }

    function loadDB() {
        try {
            const raw = localStorage.getItem(DB_KEY);
            if (raw) {
                const parsed = JSON.parse(raw);
                if (parsed && Array.isArray(parsed.invoices) && Array.isArray(parsed.clients)) {
                    parsed.users = parsed.users || getSeedData().users;
                    parsed.products = parsed.products || getSeedData().products;
                    parsed.invoice_items = parsed.invoice_items || [];
                    parsed.settings = Object.assign({}, getSeedData().settings, parsed.settings || {});
                    parsed.security_logs = parsed.security_logs || [];
                    parsed.login_attempts = parsed.login_attempts || [];
                    return parsed;
                }
            }
        } catch (e) {
            console.warn('Local DB reset due to storage parse error:', e);
        }
        const seed = getSeedData();
        saveDB(seed);
        return seed;
    }

    function saveDB(db) {
        try {
            localStorage.setItem(DB_KEY, JSON.stringify(db));
        } catch (e) {
            console.error('LocalStorage quota exceeded:', e);
        }
    }

    function nextId(list) {
        if (!Array.isArray(list) || list.length === 0) return 1;
        return Math.max(...list.map((r) => parseInt(r.id, 10) || 0)) + 1;
    }

    function logSecurity(db, eventType, details, username = null) {
        db.security_logs.unshift({
            id: nextId(db.security_logs),
            event_type: eventType,
            ip_address: 'Browser-Client',
            username: username || 'System',
            details: String(details || ''),
            user_agent: (navigator.userAgent || 'Browser').substring(0, 180),
            created_at: nowSql()
        });
        if (db.security_logs.length > 100) {
            db.security_logs = db.security_logs.slice(0, 100);
        }
    }

    // Lightweight client-side WAF mirror matching security.php
    const WAF_PATTERNS = [
        { type: 'SQL_INJECTION', re: /\b(union\s+(all\s+)?select|drop\s+(table|database)|information_schema|benchmark\s*\(|sleep\s*\()\b/i },
        { type: 'XSS_ATTACK', re: /<script\b[^>]*>|javascript:\s*[^\s]+|\bon(load|error|click)\s*=/i },
        { type: 'RCE_ATTACK', re: /<\?php|\b(passthru|shell_exec|proc_open|eval)\s*\(/i }
    ];

    function inspectPayload(db, dataObj) {
        if (!dataObj || typeof dataObj !== 'object') return null;
        for (const [k, v] of Object.entries(dataObj)) {
            if (k === 'payment_receipt' || k === 'backup_file') continue;
            if (typeof v === 'string') {
                for (const p of WAF_PATTERNS) {
                    if (p.re.test(v)) {
                        logSecurity(db, 'ATTACK_BLOCKED', `Blocked ${p.type} detected in ${k}: ${v.substring(0, 80)}`);
                        saveDB(db);
                        return p.type;
                    }
                }
            } else if (Array.isArray(v)) {
                for (const item of v) {
                    const hit = inspectPayload(db, item);
                    if (hit) return hit;
                }
            }
        }
        return null;
    }

    function getSessionUser(autoInit = true) {
        try {
            const raw = localStorage.getItem(SESSION_KEY);
            if (raw) {
                const u = JSON.parse(raw);
                if (u && u.id) return u;
            }
        } catch (e) {}

        // Auto-open dashboard on first visit unless user explicitly logged out
        if (autoInit && localStorage.getItem(LOGOUT_FLAG_KEY) !== '1') {
            const db = loadDB();
            const firstAdmin = db.users.find((u) => u.role === 'superadmin') || db.users[0];
            if (firstAdmin) {
                const sess = {
                    id: firstAdmin.id,
                    username: firstAdmin.username,
                    full_name: firstAdmin.full_name,
                    role: firstAdmin.role,
                    email: firstAdmin.email || ''
                };
                localStorage.setItem(SESSION_KEY, JSON.stringify(sess));
                return sess;
            }
        }
        return null;
    }

    function setSessionUser(user) {
        if (user) {
            localStorage.removeItem(LOGOUT_FLAG_KEY);
            localStorage.setItem(SESSION_KEY, JSON.stringify(user));
        } else {
            localStorage.removeItem(SESSION_KEY);
        }
    }

    function readFileAsDataUrl(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = () => resolve(reader.result);
            reader.onerror = () => reject(new Error('Failed to read uploaded file'));
            reader.readAsDataURL(file);
        });
    }

    function readFileAsText(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = () => resolve(reader.result);
            reader.onerror = () => reject(new Error('Failed to read file'));
            reader.readAsText(file);
        });
    }

    function ok(data = null, message = 'Success') {
        return { success: true, message, data };
    }

    function err(message = 'Error', code = 400) {
        return { success: false, message, code };
    }

    function enrichInvoice(db, inv) {
        const client = db.clients.find((c) => Number(c.id) === Number(inv.client_id));
        const itemsCount = db.invoice_items.filter((it) => Number(it.invoice_id) === Number(inv.id)).length;
        return Object.assign({}, inv, {
            client_name: client ? client.name : 'Walk-in Client',
            contact_person: client ? (client.contact_person || '') : '',
            client_phone: client ? (client.phone || '') : '',
            client_email: client ? (client.email || '') : '',
            client_address: client ? (client.address || '') : '',
            client_tax: client ? (client.tax_number || '') : '',
            items_count: itemsCount
        });
    }

    async function handleApiRequest(urlStr, init = {}) {
        const url = new URL(urlStr, window.location.href);
        const query = Object.fromEntries(url.searchParams.entries());
        let body = {};
        let uploadedReceiptFile = null;
        let uploadedBackupFile = null;

        if (init && init.body) {
            if (init.body instanceof FormData) {
                for (const [k, v] of init.body.entries()) {
                    if (v instanceof File || v instanceof Blob) {
                        if (k === 'payment_receipt' && v.size > 0) uploadedReceiptFile = v;
                        if (k === 'backup_file' && v.size > 0) uploadedBackupFile = v;
                    } else {
                        body[k] = v;
                    }
                }
            } else if (typeof init.body === 'string') {
                try {
                    body = JSON.parse(init.body);
                } catch (e) {
                    const sp = new URLSearchParams(init.body);
                    body = Object.fromEntries(sp.entries());
                }
            }
        }

        const action = query.action || body.action || '';
        const db = loadDB();

        // WAF check on text inputs
        const wafHit = inspectPayload(db, body);
        if (wafHit) {
            return err(`Security check blocked request: ${wafHit} pattern detected. Please avoid executable script tags or SQL comment characters.`);
        }

        switch (action) {
            // ==========================================
            // Authentication
            // ==========================================
            case 'login': {
                const username = String(body.username || '').trim();
                const password = String(body.password || '').trim();
                if (!username || !password) return err('Please enter both username and password.');

                const cutoff = Date.now() - 15 * 60 * 1000;
                db.login_attempts = db.login_attempts.filter((a) => a.ts > cutoff);
                const fails = db.login_attempts.filter((a) => a.username === username).length;
                if (fails >= 5) {
                    logSecurity(db, 'BRUTE_FORCE_LOCKOUT', `Account locked after ${fails} failed attempts.`, username);
                    saveDB(db);
                    return err('Too many failed login attempts. Your account has been temporarily locked for 15 minutes.', 429);
                }

                const user = db.users.find((u) => u.username === username);
                if (!user || user.password !== password) {
                    db.login_attempts.push({ username, ts: Date.now() });
                    logSecurity(db, 'LOGIN_FAILED', `Failed login attempt for username '${username}'`, username);
                    saveDB(db);
                    return err(user ? 'Invalid password. Please try again.' : `User not found with username "${username}".`);
                }

                db.login_attempts = db.login_attempts.filter((a) => a.username !== username);
                const sess = {
                    id: user.id,
                    username: user.username,
                    full_name: user.full_name,
                    role: user.role,
                    email: user.email || ''
                };
                setSessionUser(sess);
                logSecurity(db, 'LOGIN_SUCCESS', 'Successful authentication', username);
                saveDB(db);
                return ok({ user: sess }, `Welcome back, ${user.full_name}`);
            }

            case 'logout': {
                localStorage.setItem(LOGOUT_FLAG_KEY, '1');
                setSessionUser(null);
                return ok(null, 'Logged out successfully.');
            }

            case 'get_current_user': {
                return ok(getSessionUser(false));
            }

            // ==========================================
            // Admin Users Management
            // ==========================================
            case 'get_users': {
                const list = db.users
                    .slice()
                    .sort((a, b) => a.id - b.id)
                    .map(({ password, ...safe }) => safe);
                return ok(list);
            }

            case 'save_user': {
                const id = parseInt(body.id, 10) || 0;
                const username = String(body.username || '').trim();
                const full_name = String(body.full_name || '').trim();
                const email = String(body.email || '').trim();
                const role = ['superadmin', 'admin'].includes(body.role) ? body.role : 'admin';
                const password = String(body.password || '').trim();

                if (!username || !full_name) return err('Username and Full Name are required.');
                if (db.users.some((u) => u.username === username && Number(u.id) !== id)) {
                    return err(`Username '${username}' is already in use by another account.`);
                }

                const now = nowSql();
                if (id === 0) {
                    if (!password) return err('Password is required for new admin users.');
                    const newId = nextId(db.users);
                    db.users.push({ id: newId, username, password, full_name, role, email, created_at: now, updated_at: now });
                    saveDB(db);
                    return ok({ id: newId }, 'Admin user created successfully.');
                } else {
                    const existing = db.users.find((u) => Number(u.id) === id);
                    if (!existing) return err('User not found.');
                    existing.username = username;
                    existing.full_name = full_name;
                    existing.role = role;
                    existing.email = email;
                    existing.updated_at = now;
                    if (password) existing.password = password;

                    const sess = getSessionUser(false);
                    if (sess && Number(sess.id) === id) {
                        setSessionUser({ id, username, full_name, role, email });
                    }
                    saveDB(db);
                    return ok({ id }, 'Admin user updated successfully.');
                }
            }

            case 'delete_user': {
                const id = parseInt(body.id, 10) || 0;
                if (!id) return err('User ID is required.');
                const sess = getSessionUser(false);
                if (sess && Number(sess.id) === id) {
                    return err('You cannot delete your own currently active account.');
                }
                const target = db.users.find((u) => Number(u.id) === id);
                if (target && target.role === 'superadmin') {
                    const others = db.users.filter((u) => u.role === 'superadmin' && Number(u.id) !== id);
                    if (others.length < 1) return err('Cannot delete the last remaining Superadmin account.');
                }
                db.users = db.users.filter((u) => Number(u.id) !== id);
                saveDB(db);
                return ok(null, 'Admin user deleted successfully.');
            }

            case 'change_my_password': {
                const sess = getSessionUser(true);
                if (!sess) return err('Unauthorized', 401);
                const cur = String(body.current_password || '').trim();
                const next = String(body.new_password || '').trim();
                if (!next || next.length < 6) return err('New password must be at least 6 characters long.');
                const user = db.users.find((u) => Number(u.id) === Number(sess.id));
                if (!user || user.password !== cur) return err('Current password is incorrect.');
                user.password = next;
                user.updated_at = nowSql();
                saveDB(db);
                return ok(null, 'Password changed successfully!');
            }

            // ==========================================
            // Dashboard Stats
            // ==========================================
            case 'get_dashboard_stats': {
                const active = db.invoices.filter((i) => i.status !== 'cancelled');
                const total_sales = round2(active.reduce((s, i) => s + (parseFloat(i.grand_total) || 0), 0));
                const paid_total = round2(active.reduce((s, i) => s + (parseFloat(i.paid_amount) || 0), 0));
                const remaining_balance_total = round2(active.reduce((s, i) => s + (parseFloat(i.remaining_balance) || 0), 0));

                let ibraheem_total = 0;
                let fawas_total = 0;
                for (const inv of active) {
                    const sh = computeShares(inv.partner_assigned || 'both', inv.paid_amount);
                    ibraheem_total = round2(ibraheem_total + sh.ibraheem);
                    fawas_total = round2(fawas_total + sh.fawas);
                }

                const status_counts = {
                    paid: db.invoices.filter((i) => i.status === 'paid').length,
                    partial: db.invoices.filter((i) => i.status === 'partial').length,
                    pending: db.invoices.filter((i) => i.status === 'pending').length,
                    unpaid: db.invoices.filter((i) => i.status === 'unpaid').length,
                    cancelled: db.invoices.filter((i) => i.status === 'cancelled').length
                };

                const partner_counts = {
                    both: db.invoices.filter((i) => (i.partner_assigned || 'both') === 'both').length,
                    ibraheem: db.invoices.filter((i) => i.partner_assigned === 'ibraheem').length,
                    fawas: db.invoices.filter((i) => i.partner_assigned === 'fawas').length
                };

                const recent_invoices = db.invoices
                    .slice()
                    .sort((a, b) => (b.issue_date || '').localeCompare(a.issue_date || '') || (b.id - a.id))
                    .slice(0, 8)
                    .map((i) => enrichInvoice(db, i));

                const clients_summary = db.clients
                    .map((c) => {
                        const cInvs = db.invoices.filter((i) => Number(i.client_id) === Number(c.id));
                        const cActive = cInvs.filter((i) => i.status !== 'cancelled');
                        return {
                            id: c.id,
                            name: c.name,
                            contact_person: c.contact_person || '',
                            phone: c.phone || '',
                            address: c.address || '',
                            invoice_count: cInvs.length,
                            total_price: round2(cActive.reduce((s, i) => s + (parseFloat(i.grand_total) || 0), 0)),
                            total_paid: round2(cActive.reduce((s, i) => s + (parseFloat(i.paid_amount) || 0), 0)),
                            remaining_balance: round2(cActive.reduce((s, i) => s + (parseFloat(i.remaining_balance) || 0), 0))
                        };
                    })
                    .sort((a, b) => b.remaining_balance - a.remaining_balance || b.total_price - a.total_price);

                return ok({
                    total_sales,
                    paid_total,
                    remaining_balance_total,
                    ibraheem_total,
                    fawas_total,
                    invoice_count: db.invoices.length,
                    client_count: db.clients.length,
                    product_count: db.products.length,
                    status_counts,
                    partner_counts,
                    recent_invoices,
                    clients_summary
                });
            }

            // ==========================================
            // Invoices CRUD
            // ==========================================
            case 'get_invoices': {
                const status = String(query.status || '').trim();
                const partner = String(query.partner || '').trim();
                const search = String(query.search || '').trim().toLowerCase();
                const fromDate = String(query.from_date || '').trim();
                const toDate = String(query.to_date || '').trim();
                const clientId = parseInt(query.client_id, 10) || 0;

                let list = db.invoices.map((i) => enrichInvoice(db, i));

                if (status && status !== 'all') list = list.filter((i) => i.status === status);
                if (partner && partner !== 'all') list = list.filter((i) => i.partner_assigned === partner);
                if (clientId > 0) list = list.filter((i) => Number(i.client_id) === clientId);
                if (search) {
                    list = list.filter((i) =>
                        String(i.invoice_number || '').toLowerCase().includes(search) ||
                        String(i.client_name || '').toLowerCase().includes(search) ||
                        String(i.contact_person || '').toLowerCase().includes(search) ||
                        String(i.client_phone || '').toLowerCase().includes(search)
                    );
                }
                if (fromDate) list = list.filter((i) => (i.issue_date || '') >= fromDate);
                if (toDate) list = list.filter((i) => (i.issue_date || '') <= toDate);

                list.sort((a, b) => (b.issue_date || '').localeCompare(a.issue_date || '') || (b.id - a.id));
                return ok(list);
            }

            case 'get_invoice': {
                const id = parseInt(query.id, 10) || 0;
                if (!id) return err('Invoice ID is required');
                const inv = db.invoices.find((i) => Number(i.id) === id);
                if (!inv) return err('Invoice not found', 404);
                const full = enrichInvoice(db, inv);
                full.issue_date = (full.issue_date || '').substring(0, 10);
                full.due_date = (full.due_date || '').substring(0, 10);
                full.items = db.invoice_items.filter((it) => Number(it.invoice_id) === id);
                full.settings = db.settings;
                return ok(full);
            }

            case 'save_invoice': {
                const id = parseInt(body.id, 10) || 0;
                let invoiceNumber = String(body.invoice_number || '').trim();
                let clientId = body.client_id ? parseInt(body.client_id, 10) : null;
                const newClientName = String(body.new_client_name || '').trim();
                const newContactPerson = String(body.new_contact_person || '').trim();
                const newClientPhone = String(body.new_client_phone || '').trim();
                const newClientAddress = String(body.new_client_address || '').trim();
                const issueDate = String(body.issue_date || todayDate()).trim();
                const dueDate = String(body.due_date || todayDate(15)).trim();
                let status = String(body.status || 'unpaid').trim();
                const paymentMethod = String(body.payment_method || 'gcash').trim();
                const paymentTerm = String(body.payment_term || 'full').trim();
                let installmentMonths = Math.max(1, parseInt(body.installment_months, 10) || 1);
                if (paymentTerm === '3_months') installmentMonths = 3;
                if (paymentTerm === '6_months') installmentMonths = 6;
                const partnerAssigned = ['ibraheem', 'fawas', 'both'].includes(body.partner_assigned) ? body.partner_assigned : 'both';

                let priceNote = String(body.price_note || '').trim();
                const amountDueNote = String(body.amount_due_note || '').trim();
                const notes = String(body.notes || '').trim();
                const items = Array.isArray(body.items) ? body.items : [];
                const paidAmount = Math.max(0, parseFloat(body.paid_amount) || 0);
                const hasManualBalance = body.remaining_balance !== undefined && body.remaining_balance !== '' && body.remaining_balance !== null;
                const manualBalance = hasManualBalance ? parseFloat(body.remaining_balance) : null;

                if (!items.length) return err('Invoice must contain at least one line item');

                if (!clientId && newClientName) {
                    clientId = nextId(db.clients);
                    db.clients.push({
                        id: clientId,
                        name: newClientName,
                        contact_person: newContactPerson,
                        phone: newClientPhone,
                        email: '',
                        address: newClientAddress,
                        tax_number: '',
                        created_at: nowSql()
                    });
                }

                if (!invoiceNumber) {
                    invoiceNumber = String(nextId(db.invoices)).padStart(5, '0');
                }

                let subtotal = 0;
                let totalDiscount = 0;
                let totalTax = 0;
                const normalizedItems = [];

                for (const item of items) {
                    const itemName = String(item.item_name || '').trim();
                    if (!itemName) continue;
                    const desc = String(item.description || '').trim();
                    const qty = Math.max(0.001, parseFloat(item.quantity) || 1);
                    const price = Math.max(0, parseFloat(item.unit_price) || 0);
                    const disc = Math.max(0, parseFloat(item.discount) || 0);
                    const taxRate = Math.max(0, parseFloat(item.tax_rate) || 0);

                    const lineSub = qty * price;
                    const lineDisc = Math.min(lineSub, disc);
                    const taxable = Math.max(0, lineSub - lineDisc);
                    const lineTax = (taxable * taxRate) / 100;
                    const lineTot = round2(taxable + lineTax);

                    subtotal += lineSub;
                    totalDiscount += lineDisc;
                    totalTax += lineTax;

                    normalizedItems.push({
                        item_name: itemName,
                        description: desc,
                        quantity: qty,
                        unit_price: price,
                        discount: round2(lineDisc),
                        tax_rate: taxRate,
                        total: lineTot
                    });
                }

                if (!normalizedItems.length) return err('Please ensure item details are filled out correctly');

                const grandTotal = round2(subtotal - totalDiscount + totalTax);
                const monthlyPayment = round2(grandTotal / installmentMonths);
                const finalBalance = hasManualBalance ? round2(manualBalance) : Math.max(0, round2(grandTotal - paidAmount));
                const shares = computeShares(partnerAssigned, paidAmount);

                if (status !== 'cancelled') {
                    if (finalBalance <= 0 && paidAmount >= grandTotal && grandTotal > 0) status = 'paid';
                    else if (paidAmount > 0 && finalBalance > 0) status = 'partial';
                }

                if (!priceNote && installmentMonths > 1) {
                    priceNote = `PHP ${fmtNum(grandTotal)} / ${installmentMonths} Months = PHP ${fmtNum(monthlyPayment)} / Month`;
                }

                let invoiceId = id;
                if (id > 0) {
                    const existing = db.invoices.find((i) => Number(i.id) === id);
                    if (!existing) return err('Invoice not found', 404);
                    Object.assign(existing, {
                        invoice_number: invoiceNumber,
                        client_id: clientId,
                        issue_date: issueDate,
                        due_date: dueDate,
                        status,
                        payment_method: paymentMethod,
                        payment_term: paymentTerm,
                        installment_months: installmentMonths,
                        monthly_payment: monthlyPayment,
                        partner_assigned: partnerAssigned,
                        ibraheem_share: shares.ibraheem,
                        fawas_share: shares.fawas,
                        subtotal: round2(subtotal),
                        discount: round2(totalDiscount),
                        tax_amount: round2(totalTax),
                        grand_total: grandTotal,
                        paid_amount: round2(paidAmount),
                        remaining_balance: finalBalance,
                        price_note: priceNote,
                        amount_due_note: amountDueNote,
                        notes
                    });
                    db.invoice_items = db.invoice_items.filter((it) => Number(it.invoice_id) !== id);
                } else {
                    if (db.invoices.some((i) => i.invoice_number === invoiceNumber)) {
                        invoiceNumber += '-' + Math.floor(10 + Math.random() * 89);
                    }
                    invoiceId = nextId(db.invoices);
                    db.invoices.push({
                        id: invoiceId,
                        invoice_number: invoiceNumber,
                        client_id: clientId,
                        issue_date: issueDate,
                        due_date: dueDate,
                        status,
                        payment_method: paymentMethod,
                        payment_term: paymentTerm,
                        installment_months: installmentMonths,
                        monthly_payment: monthlyPayment,
                        partner_assigned: partnerAssigned,
                        ibraheem_share: shares.ibraheem,
                        fawas_share: shares.fawas,
                        subtotal: round2(subtotal),
                        discount: round2(totalDiscount),
                        tax_amount: round2(totalTax),
                        grand_total: grandTotal,
                        paid_amount: round2(paidAmount),
                        remaining_balance: finalBalance,
                        price_note: priceNote,
                        amount_due_note: amountDueNote,
                        package_includes: '',
                        notes,
                        payment_receipt: null,
                        submitted_by: 'admin',
                        created_at: nowSql()
                    });
                }

                for (const it of normalizedItems) {
                    db.invoice_items.push(Object.assign({ id: nextId(db.invoice_items), invoice_id: invoiceId }, it));
                }

                saveDB(db);
                return ok({
                    invoice_id: invoiceId,
                    invoice_number: invoiceNumber,
                    grand_total: grandTotal,
                    paid_amount: round2(paidAmount),
                    remaining_balance: finalBalance,
                    monthly_payment: monthlyPayment,
                    installment_months: installmentMonths,
                    partner_assigned: partnerAssigned,
                    ibraheem_share: shares.ibraheem,
                    fawas_share: shares.fawas
                }, 'Invoice saved successfully');
            }

            case 'submit_client_invoice': {
                const clientName = String(body.client_name || '').trim();
                const contactPerson = String(body.contact_person || '').trim();
                const phone = String(body.phone || '').trim();
                const email = String(body.email || '').trim();
                const address = String(body.address || '').trim();
                const serviceName = String(body.service_name || 'E-Commerce & Web Maintenance Services').trim();
                let serviceDesc = String(body.service_description || '').trim();
                const price = Math.max(0, parseFloat(body.price || body.total_amount) || 7500);
                const paymentMethod = ['gcash', 'cash'].includes(body.payment_method) ? body.payment_method : 'gcash';
                const paymentTerm = String(body.payment_term || 'full').trim();
                let installmentMonths = Math.max(1, parseInt(body.installment_months, 10) || 1);
                if (paymentTerm === '3_months') installmentMonths = 3;
                if (paymentTerm === '6_months') installmentMonths = 6;
                const monthlyPayment = round2(price / installmentMonths);
                const partnerAssigned = ['ibraheem', 'fawas', 'both'].includes(body.partner_assigned) ? body.partner_assigned : 'both';
                const notes = String(body.notes || '').trim();

                if (!clientName) return err('Company or Client name is required');

                let client = db.clients.find((c) => c.name.toLowerCase() === clientName.toLowerCase() || (phone && c.phone === phone));
                if (client) {
                    if (contactPerson) client.contact_person = contactPerson;
                    if (address) client.address = address;
                    if (email) client.email = email;
                } else {
                    client = {
                        id: nextId(db.clients),
                        name: clientName,
                        contact_person: contactPerson,
                        phone,
                        email,
                        address,
                        tax_number: '',
                        created_at: nowSql()
                    };
                    db.clients.push(client);
                }

                const invoiceId = nextId(db.invoices);
                const invoiceNumber = String(invoiceId).padStart(5, '0');
                if (!serviceDesc) {
                    serviceDesc = 'Package of E-Commerce Maintenance Services and Web maintenance including Rescue System.\n--- Nothing Follows ---';
                }

                const formulaText = installmentMonths > 1
                    ? `Installment Plan: PHP ${fmtNum(price)} ÷ ${installmentMonths} Months = PHP ${fmtNum(monthlyPayment)} / Month`
                    : `Full Payment: PHP ${fmtNum(price)}`;

                const partnerLabel = partnerAssigned === 'both'
                    ? 'Both (Mr. Ibraheem & Mr. Fawas - 50/50 Split)'
                    : (partnerAssigned === 'ibraheem' ? 'Mr. Ibraheem' : 'Mr. Fawas');

                let clientNote = `Requested via Client Invoice Portal.\nPayment Method: ${paymentMethod.toUpperCase()} (GCash: ${db.settings.gcash_account_name || 'Ferdenand Sabado'} - ${db.settings.company_gcash || '09617190588'})\nAssigned Account: ${partnerLabel}\n${formulaText}\n${notes}`;

                let receiptDataUrl = null;
                if (uploadedReceiptFile) {
                    try {
                        receiptDataUrl = await readFileAsDataUrl(uploadedReceiptFile);
                        clientNote += `\n[GCash Payment Proof Attached: ${uploadedReceiptFile.name || 'gcash_receipt.jpg'}]`;
                    } catch (e) {
                        receiptDataUrl = null;
                    }
                }

                db.invoices.push({
                    id: invoiceId,
                    invoice_number: invoiceNumber,
                    client_id: client.id,
                    issue_date: todayDate(),
                    due_date: todayDate(15),
                    status: 'pending',
                    payment_method: paymentMethod,
                    payment_term: paymentTerm,
                    installment_months: installmentMonths,
                    monthly_payment: monthlyPayment,
                    partner_assigned: partnerAssigned,
                    ibraheem_share: 0,
                    fawas_share: 0,
                    subtotal: round2(price),
                    discount: 0,
                    tax_amount: 0,
                    grand_total: round2(price),
                    paid_amount: 0,
                    remaining_balance: round2(price),
                    price_note: formulaText,
                    amount_due_note: installmentMonths > 1 ? `Monthly: PHP ${fmtNum(monthlyPayment)}` : `PHP ${fmtNum(price)}`,
                    package_includes: serviceDesc,
                    notes: clientNote,
                    payment_receipt: receiptDataUrl,
                    submitted_by: 'client',
                    created_at: nowSql()
                });

                db.invoice_items.push({
                    id: nextId(db.invoice_items),
                    invoice_id: invoiceId,
                    item_name: serviceName,
                    description: serviceDesc,
                    quantity: 1,
                    unit_price: round2(price),
                    discount: 0,
                    tax_rate: 0,
                    total: round2(price)
                });

                saveDB(db);
                return ok({
                    invoice_id: invoiceId,
                    invoice_number: invoiceNumber,
                    client_name: clientName,
                    grand_total: round2(price),
                    remaining_balance: round2(price),
                    payment_term: paymentTerm,
                    installment_months: installmentMonths,
                    monthly_payment: monthlyPayment,
                    partner_assigned: partnerAssigned,
                    payment_receipt: receiptDataUrl,
                    gcash_name: db.settings.gcash_account_name || 'Ferdenand Sabado',
                    gcash_number: db.settings.company_gcash || '09617190588'
                }, `Invoice request submitted successfully! Your invoice number is ${invoiceNumber}`);
            }

            case 'delete_invoice': {
                const id = parseInt(body.id, 10) || 0;
                if (!id) return err('Invoice ID is required');
                db.invoice_items = db.invoice_items.filter((it) => Number(it.invoice_id) !== id);
                db.invoices = db.invoices.filter((i) => Number(i.id) !== id);
                saveDB(db);
                return ok(null, 'Invoice deleted successfully');
            }

            case 'update_invoice_status': {
                const id = parseInt(body.id, 10) || 0;
                const status = String(body.status || '').trim();
                if (!id || !['paid', 'partial', 'unpaid', 'pending', 'cancelled'].includes(status)) {
                    return err('Invalid status value');
                }
                const inv = db.invoices.find((i) => Number(i.id) === id);
                if (!inv) return err('Invoice not found');
                inv.status = status;
                if (status === 'paid') {
                    inv.paid_amount = round2(inv.grand_total);
                    inv.remaining_balance = 0;
                    const sh = computeShares(inv.partner_assigned || 'both', inv.paid_amount);
                    inv.ibraheem_share = sh.ibraheem;
                    inv.fawas_share = sh.fawas;
                }
                saveDB(db);
                return ok(null, 'Invoice status updated successfully');
            }

            case 'update_partner_assignment': {
                const id = parseInt(body.id || body.invoice_id, 10) || 0;
                const partner = ['ibraheem', 'fawas', 'both'].includes(body.partner_assigned) ? body.partner_assigned : 'both';
                if (!id) return err('Invoice ID is required');
                const inv = db.invoices.find((i) => Number(i.id) === id);
                if (!inv) return err('Invoice not found');
                const sh = computeShares(partner, inv.paid_amount);
                inv.partner_assigned = partner;
                inv.ibraheem_share = sh.ibraheem;
                inv.fawas_share = sh.fawas;
                saveDB(db);
                return ok({ id, partner_assigned: partner, ibraheem_share: sh.ibraheem, fawas_share: sh.fawas }, 'Partner assignment updated successfully');
            }

            case 'record_payment': {
                const id = parseInt(body.id || body.invoice_id, 10) || 0;
                const payment = Math.max(0, parseFloat(body.payment_amount) || 0);
                const paymentMethod = String(body.payment_method || 'gcash').trim();
                const paymentNote = String(body.payment_note || '').trim();
                if (!id) return err('Invoice ID is required');
                const inv = db.invoices.find((i) => Number(i.id) === id);
                if (!inv) return err('Invoice not found');

                const newPaid = round2((parseFloat(inv.paid_amount) || 0) + payment);
                const newBalance = Math.max(0, round2((parseFloat(inv.grand_total) || 0) - newPaid));
                const newStatus = newBalance <= 0 ? 'paid' : 'partial';
                const sh = computeShares(inv.partner_assigned || 'both', newPaid);

                inv.paid_amount = newPaid;
                inv.remaining_balance = newBalance;
                inv.status = newStatus;
                inv.payment_method = paymentMethod;
                inv.ibraheem_share = sh.ibraheem;
                inv.fawas_share = sh.fawas;
                if (paymentNote) {
                    inv.notes = (inv.notes || '') + `\n[Payment Recorded: PHP ${fmtNum(payment)} via ${paymentMethod.toUpperCase()} - ${todayDate()}: ${paymentNote}]`;
                }
                saveDB(db);
                return ok({
                    paid_amount: newPaid,
                    remaining_balance: newBalance,
                    status: newStatus,
                    ibraheem_share: sh.ibraheem,
                    fawas_share: sh.fawas
                }, 'Payment recorded successfully');
            }

            // ==========================================
            // Clients CRUD
            // ==========================================
            case 'get_clients': {
                const search = String(query.search || '').trim().toLowerCase();
                let list = db.clients.map((c) => {
                    const cInvs = db.invoices.filter((i) => Number(i.client_id) === Number(c.id));
                    const cActive = cInvs.filter((i) => i.status !== 'cancelled');
                    return Object.assign({}, c, {
                        invoice_count: cInvs.length,
                        total_purchases: round2(cActive.reduce((s, i) => s + (parseFloat(i.grand_total) || 0), 0)),
                        total_paid: round2(cActive.reduce((s, i) => s + (parseFloat(i.paid_amount) || 0), 0)),
                        total_balance: round2(cActive.reduce((s, i) => s + (parseFloat(i.remaining_balance) || 0), 0))
                    });
                });
                if (search) {
                    list = list.filter((c) =>
                        String(c.name || '').toLowerCase().includes(search) ||
                        String(c.contact_person || '').toLowerCase().includes(search) ||
                        String(c.phone || '').toLowerCase().includes(search) ||
                        String(c.address || '').toLowerCase().includes(search)
                    );
                }
                list.sort((a, b) => b.total_balance - a.total_balance || b.id - a.id);
                return ok(list);
            }

            case 'get_client': {
                const id = parseInt(query.id, 10) || 0;
                if (!id) return err('Client ID is required');
                const c = db.clients.find((x) => Number(x.id) === id);
                if (!c) return err('Client not found', 404);
                const cInvs = db.invoices
                    .filter((i) => Number(i.client_id) === id)
                    .sort((a, b) => (b.issue_date || '').localeCompare(a.issue_date || '') || (b.id - a.id));
                const cActive = cInvs.filter((i) => i.status !== 'cancelled');
                return ok(Object.assign({}, c, {
                    invoice_count: cInvs.length,
                    total_purchases: round2(cActive.reduce((s, i) => s + (parseFloat(i.grand_total) || 0), 0)),
                    total_paid: round2(cActive.reduce((s, i) => s + (parseFloat(i.paid_amount) || 0), 0)),
                    total_balance: round2(cActive.reduce((s, i) => s + (parseFloat(i.remaining_balance) || 0), 0)),
                    invoices: cInvs
                }));
            }

            case 'save_client': {
                const id = parseInt(body.id, 10) || 0;
                const name = String(body.name || '').trim();
                const contact_person = String(body.contact_person || '').trim();
                const phone = String(body.phone || '').trim();
                const email = String(body.email || '').trim();
                const address = String(body.address || '').trim();
                const tax_number = String(body.tax_number || '').trim();
                if (!name) return err('Company or client name is required');

                if (id > 0) {
                    const existing = db.clients.find((c) => Number(c.id) === id);
                    if (!existing) return err('Client not found');
                    Object.assign(existing, { name, contact_person, phone, email, address, tax_number });
                    saveDB(db);
                    return ok({ id }, 'Client information saved successfully');
                } else {
                    const newId = nextId(db.clients);
                    db.clients.push({ id: newId, name, contact_person, phone, email, address, tax_number, created_at: nowSql() });
                    saveDB(db);
                    return ok({ id: newId }, 'Client information saved successfully');
                }
            }

            case 'delete_client': {
                const id = parseInt(body.id, 10) || 0;
                if (!id) return err('Client ID is required');
                db.invoices.forEach((i) => {
                    if (Number(i.client_id) === id) i.client_id = null;
                });
                db.clients = db.clients.filter((c) => Number(c.id) !== id);
                saveDB(db);
                return ok(null, 'Client deleted successfully');
            }

            // ==========================================
            // Products CRUD
            // ==========================================
            case 'get_products': {
                const list = db.products.slice().sort((a, b) => (a.name || '').localeCompare(b.name || ''));
                return ok(list);
            }

            case 'save_product': {
                const id = parseInt(body.id, 10) || 0;
                const name = String(body.name || '').trim();
                const price = parseFloat(body.price) || 0;
                const tax_rate = parseFloat(body.tax_rate) || 0;
                const description = String(body.description || '').trim();
                if (!name) return err('Product/Service name is required');

                if (id > 0) {
                    const existing = db.products.find((p) => Number(p.id) === id);
                    if (!existing) return err('Product not found');
                    Object.assign(existing, { name, price, tax_rate, description });
                    saveDB(db);
                    return ok({ id }, 'Product/Service saved successfully');
                } else {
                    const newId = nextId(db.products);
                    db.products.push({ id: newId, name, price, tax_rate, description, created_at: nowSql() });
                    saveDB(db);
                    return ok({ id: newId }, 'Product/Service saved successfully');
                }
            }

            case 'delete_product': {
                const id = parseInt(body.id, 10) || 0;
                if (!id) return err('Product ID is required');
                db.products = db.products.filter((p) => Number(p.id) !== id);
                saveDB(db);
                return ok(null, 'Product/Service deleted successfully');
            }

            // ==========================================
            // Settings
            // ==========================================
            case 'get_settings': {
                return ok(db.settings);
            }

            case 'save_settings': {
                const allowed = [
                    'company_name', 'company_cr', 'company_vat', 'company_phone',
                    'company_email', 'company_address', 'company_manager', 'company_title',
                    'company_cert', 'company_website', 'company_gcash', 'gcash_account_name',
                    'partner_1_name', 'partner_2_name', 'company_stc', 'company_logo',
                    'currency', 'currency_symbol', 'default_tax_rate', 'invoice_prefix', 'invoice_footer'
                ];
                for (const k of allowed) {
                    if (body[k] !== undefined) {
                        db.settings[k] = String(body[k]).trim();
                    }
                }
                saveDB(db);
                return ok(null, 'Settings saved successfully');
            }

            // ==========================================
            // Backup Import
            // ==========================================
            case 'import_backup': {
                if (!uploadedBackupFile) return err('No backup file uploaded.');
                const text = await readFileAsText(uploadedBackupFile);
                if (/<\?php|<script\b|\beval\s*\(/i.test(text)) {
                    logSecurity(db, 'MALWARE_REJECTED', `Malicious code detected in uploaded backup: ${uploadedBackupFile.name}`);
                    saveDB(db);
                    return err('Security Threat Detected: File contains unauthorized code or executable scripts.');
                }
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    return err('File is corrupted or not a valid JSON document.');
                }
                if (!data || !Array.isArray(data.invoices) || !Array.isArray(data.clients)) {
                    return err('Invalid backup file structure: missing required database collections.');
                }
                db.clients = data.clients;
                db.products = Array.isArray(data.products) ? data.products : db.products;
                db.invoices = data.invoices;
                db.invoice_items = Array.isArray(data.items) ? data.items : [];
                if (data.settings && typeof data.settings === 'object') {
                    db.settings = Object.assign({}, db.settings, data.settings);
                }
                saveDB(db);
                return ok(null, 'Backup data restored successfully');
            }

            // ==========================================
            // Security Logs
            // ==========================================
            case 'get_security_logs': {
                const cutoff = Date.now() - 15 * 60 * 1000;
                const activeLockouts = db.login_attempts.filter((a) => a.ts > cutoff).length;
                return ok({
                    logs: db.security_logs.slice(0, 50),
                    stats: {
                        total_attacks_blocked: db.security_logs.filter((l) => l.event_type === 'ATTACK_BLOCKED').length,
                        total_failed_logins: db.security_logs.filter((l) => l.event_type === 'LOGIN_FAILED').length,
                        total_lockouts: db.security_logs.filter((l) => l.event_type === 'BRUTE_FORCE_LOCKOUT').length,
                        active_lockouts: activeLockouts
                    }
                });
            }

            case 'clear_security_logs': {
                const sess = getSessionUser(true);
                if (!sess || sess.role !== 'superadmin') {
                    return err('Only Superadmin can purge security audit logs.');
                }
                db.security_logs = [];
                db.login_attempts = [];
                saveDB(db);
                return ok(null, 'Security logs cleared successfully.');
            }

            default:
                return err(`Invalid API action: ${action}`, 404);
        }
    }

    // Intercept fetch() calls to api.php on static pages
    const nativeFetch = window.fetch.bind(window);
    window.fetch = async function (input, init) {
        const urlStr = typeof input === 'string' ? input : (input && input.url ? input.url : '');
        if (/(^|\/)api\.php(\?|$)/i.test(urlStr)) {
            const payload = await handleApiRequest(urlStr, init);
            return new Response(JSON.stringify(payload), {
                status: 200,
                headers: { 'Content-Type': 'application/json; charset=utf-8' }
            });
        }
        return nativeFetch(input, init);
    };

    // Public helpers for login.html, index.html, portal.html, print.html
    window.YaramayBackend = {
        getSessionUser,
        setSessionUser,
        getSettings: () => loadDB().settings,
        getInvoiceForPrint: (id) => {
            const db = loadDB();
            const inv = db.invoices.find((i) => Number(i.id) === Number(id));
            if (!inv) return null;
            const full = enrichInvoice(db, inv);
            full.items = db.invoice_items.filter((it) => Number(it.invoice_id) === Number(id));
            full.settings = db.settings;
            return full;
        },
        login: async (username, password) => {
            return handleApiRequest('api.php', {
                method: 'POST',
                body: JSON.stringify({ action: 'login', username, password })
            });
        },
        logout: () => {
            localStorage.setItem(LOGOUT_FLAG_KEY, '1');
            setSessionUser(null);
            window.location.href = 'login.html';
        },
        exportBackupDownload: () => {
            const db = loadDB();
            const backup = {
                version: '2.7',
                system: 'YARAMAY Computer Maintenance Services',
                timestamp: new Date().toISOString(),
                settings: db.settings,
                clients: db.clients,
                products: db.products,
                invoices: db.invoices,
                items: db.invoice_items
            };
            const blob = new Blob([JSON.stringify(backup, null, 2)], { type: 'application/json;charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `yaramay_backup_${todayDate()}.json`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        },
        importBackupFile: async (file) => {
            const fd = new FormData();
            fd.append('action', 'import_backup');
            fd.append('backup_file', file);
            return handleApiRequest('api.php', { method: 'POST', body: fd });
        },
        resetDemoData: () => {
            localStorage.removeItem(DB_KEY);
            loadDB();
        }
    };
})();
