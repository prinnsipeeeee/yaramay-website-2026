<?php
/**
 * YARAMAY Computer Maintenance Services - Invoice Record System
 * Configuration & SQLite Auto-Setup
 */

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', '0');

// ==========================================
// Database Configuration (Env / Local Override / XAMPP / Cloud)
// ==========================================
// Optional local override file for shared hosting (InfinityFree, cPanel, Hostinger).
// Copy config.local.example.php to config.local.php and fill in your host credentials.
if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'config.local.php')) {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'config.local.php';
}

// Parse MYSQL_URL / DATABASE_URL if provided by cloud hosts (e.g., Railway, Aiven, PlanetScale)
$_dbUrlRaw = getenv('MYSQL_URL') ?: getenv('DATABASE_URL') ?: '';
$_dbUrlParts = (!empty($_dbUrlRaw) && strpos($_dbUrlRaw, 'mysql') === 0) ? parse_url($_dbUrlRaw) : [];

// Set DB_DRIVER to 'mysql' (default for XAMPP & MySQL hosts) or 'sqlite' (portable file-based DB).
// If MySQL is unavailable, it gracefully falls back to SQLite.
if (!defined('DB_DRIVER')) {
    $_envDriver = getenv('DB_DRIVER');
    if ($_envDriver !== false && $_envDriver !== '') {
        define('DB_DRIVER', strtolower(trim($_envDriver)));
    } else {
        define('DB_DRIVER', 'mysql');
    }
}

if (!defined('DB_HOST')) {
    define('DB_HOST', getenv('DB_HOST') ?: getenv('MYSQLHOST') ?: ($_dbUrlParts['host'] ?? '127.0.0.1'));
}
if (!defined('DB_PORT')) {
    define('DB_PORT', (string)(getenv('DB_PORT') ?: getenv('MYSQLPORT') ?: ($_dbUrlParts['port'] ?? '3306')));
}
if (!defined('DB_NAME')) {
    $_urlPath = isset($_dbUrlParts['path']) ? ltrim($_dbUrlParts['path'], '/') : '';
    define('DB_NAME', getenv('DB_NAME') ?: getenv('MYSQLDATABASE') ?: ($_urlPath !== '' ? $_urlPath : 'yaramay_invoices'));
}
if (!defined('DB_USER')) {
    define('DB_USER', getenv('DB_USER') ?: getenv('MYSQLUSER') ?: ($_dbUrlParts['user'] ?? 'root'));
}
if (!defined('DB_PASS')) {
    $_envPass = getenv('DB_PASS');
    if ($_envPass === false) {
        $_envPass = getenv('MYSQLPASSWORD');
    }
    define('DB_PASS', $_envPass !== false ? $_envPass : (isset($_dbUrlParts['pass']) ? urldecode($_dbUrlParts['pass']) : ''));
}
if (!defined('DB_CHARSET')) {
    define('DB_CHARSET', 'utf8mb4');
}

$_customDataDir = getenv('DATA_DIR');
define('DATA_DIR', (!empty($_customDataDir)) ? rtrim($_customDataDir, "/\\") : (__DIR__ . DIRECTORY_SEPARATOR . 'data'));
define('SESSION_DIR', DATA_DIR . DIRECTORY_SEPARATOR . 'sessions');
define('DB_FILE', DATA_DIR . DIRECTORY_SEPARATOR . 'invoices.db');

if (!file_exists(DATA_DIR)) {
    @mkdir(DATA_DIR, 0775, true);
}
if (!file_exists(SESSION_DIR)) {
    @mkdir(SESSION_DIR, 0775, true);
}

// If a custom persistent DATA_DIR is mounted and empty, copy the pre-seeded SQLite DB into it
$_bundledDb = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'invoices.db';
if (DB_FILE !== $_bundledDb && !file_exists(DB_FILE) && file_exists($_bundledDb)) {
    @copy($_bundledDb, DB_FILE);
}

if (is_dir(SESSION_DIR) && is_writable(SESSION_DIR)) {
    @session_save_path(SESSION_DIR);
}

if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
        || (($_SERVER['SERVER_PORT'] ?? '') == 443);

    if (PHP_VERSION_ID >= 70300) {
        @session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => $isHttps,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }
    @session_start();
}

function getDatabaseConnection(): PDO {
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    // 1. Try MySQL Connection (Cloud / Shared Hosting / XAMPP)
    if (DB_DRIVER === 'mysql') {
        $isLocal = in_array(DB_HOST, ['127.0.0.1', 'localhost'], true);
        $timeout = $isLocal ? 2 : 5;
        $pdoOpts = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_TIMEOUT            => $timeout
        ];

        $dsnDb = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

        try {
            // Connect directly to the target database first (works on InfinityFree, cPanel, Railway, Aiven without CREATE DATABASE privileges)
            $pdo = new PDO($dsnDb, DB_USER, DB_PASS, $pdoOpts);
            $pdo->exec("SET NAMES " . DB_CHARSET . ";");
            return $pdo;
        } catch (PDOException $e) {
            // If database does not exist yet (MySQL error 1049, e.g. fresh local XAMPP), create it and reconnect
            $errMsg = $e->getMessage();
            if (strpos($errMsg, '1049') !== false || stripos($errMsg, 'Unknown database') !== false) {
                try {
                    $dsnServer = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=" . DB_CHARSET;
                    $tmpPdo = new PDO($dsnServer, DB_USER, DB_PASS, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_TIMEOUT => $timeout
                    ]);
                    $tmpPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET " . DB_CHARSET . " COLLATE utf8mb4_unicode_ci;");
                    $pdo = new PDO($dsnDb, DB_USER, DB_PASS, $pdoOpts);
                    $pdo->exec("SET NAMES " . DB_CHARSET . ";");
                    return $pdo;
                } catch (Exception $createEx) {
                    $e = $createEx;
                }
            }
            // Graceful fallback to SQLite if MySQL is stopped or unreachable
            @file_put_contents(DATA_DIR . DIRECTORY_SEPARATOR . 'db_fallback.log', date('Y-m-d H:i:s') . " MySQL failed: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
        } catch (Exception $e) {
            @file_put_contents(DATA_DIR . DIRECTORY_SEPARATOR . 'db_fallback.log', date('Y-m-d H:i:s') . " MySQL failed: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
        }
    }

    // 2. SQLite Connection (Default or Fallback)
    $dsn = 'sqlite:' . DB_FILE;
    $pdo = new PDO($dsn, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $pdo->exec('PRAGMA foreign_keys = ON;');
    return $pdo;
}

function getDatabaseDriver(): string {
    return getDatabaseConnection()->getAttribute(PDO::ATTR_DRIVER_NAME);
}

function initDatabaseSchema(): void {
    $db = getDatabaseConnection();
    $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);

    // If using MySQL (XAMPP / Cloud / Shared Host), auto-initialize from database.sql if empty
    if ($driver === 'mysql') {
        try {
            $tableCount = (int)$db->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE()")->fetchColumn();
            if ($tableCount === 0 && file_exists(__DIR__ . '/database.sql')) {
                $sql = str_replace(["\r\n", "\r"], "\n", file_get_contents(__DIR__ . '/database.sql'));
                $queries = explode(";\n", $sql);
                foreach ($queries as $q) {
                    $q = trim($q);
                    if (!empty($q)) {
                        try {
                            $db->exec($q);
                        } catch (Exception $e) {}
                    }
                }
            }
        } catch (Exception $e) {}
    } else {
        // Tables schema for SQLite
        $queries = [
            "CREATE TABLE IF NOT EXISTS settings (
                key TEXT PRIMARY KEY,
                value TEXT
            );",

        "CREATE TABLE IF NOT EXISTS clients (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            contact_person TEXT,
            phone TEXT,
            email TEXT,
            address TEXT,
            tax_number TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );",

        "CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            price REAL DEFAULT 0,
            tax_rate REAL DEFAULT 0,
            description TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );",

        "CREATE TABLE IF NOT EXISTS invoices (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            invoice_number TEXT UNIQUE NOT NULL,
            client_id INTEGER,
            issue_date DATE NOT NULL,
            due_date DATE,
            status TEXT DEFAULT 'unpaid', -- paid, partial, unpaid, pending, cancelled
            payment_method TEXT DEFAULT 'gcash', -- gcash, cash, stc_pay, bank_transfer, card, cheque
            payment_term TEXT DEFAULT 'full', -- full, 3_months, 6_months, custom
            installment_months INTEGER DEFAULT 1,
            monthly_payment REAL DEFAULT 0,
            partner_assigned TEXT DEFAULT 'both', -- ibraheem, fawas, both
            ibraheem_share REAL DEFAULT 0,
            fawas_share REAL DEFAULT 0,
            subtotal REAL DEFAULT 0,
            discount REAL DEFAULT 0,
            tax_amount REAL DEFAULT 0,
            grand_total REAL DEFAULT 0,
            paid_amount REAL DEFAULT 0,
            remaining_balance REAL DEFAULT 0,
            price_note TEXT,
            amount_due_note TEXT,
            package_includes TEXT,
            notes TEXT,
            submitted_by TEXT DEFAULT 'admin', -- admin, client
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (client_id) REFERENCES clients (id) ON DELETE SET NULL
        );",

        "CREATE TABLE IF NOT EXISTS invoice_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            invoice_id INTEGER NOT NULL,
            item_name TEXT NOT NULL,
            description TEXT,
            quantity REAL DEFAULT 1,
            unit_price REAL DEFAULT 0,
            discount REAL DEFAULT 0,
            tax_rate REAL DEFAULT 0,
            total REAL DEFAULT 0,
            FOREIGN KEY (invoice_id) REFERENCES invoices (id) ON DELETE CASCADE
        );",

        "CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            full_name TEXT NOT NULL,
            role TEXT DEFAULT 'admin', -- superadmin, admin
            email TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );",

        "CREATE TABLE IF NOT EXISTS login_attempts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ip_address TEXT NOT NULL,
            username TEXT,
            attempt_time DATETIME DEFAULT CURRENT_TIMESTAMP,
            status TEXT DEFAULT 'failed'
        );",

        "CREATE TABLE IF NOT EXISTS security_logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            event_type TEXT NOT NULL,
            ip_address TEXT NOT NULL,
            username TEXT,
            details TEXT,
            user_agent TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );",

        "CREATE TABLE IF NOT EXISTS rate_limits (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ip_address TEXT NOT NULL,
            endpoint TEXT NOT NULL,
            request_time DATETIME DEFAULT CURRENT_TIMESTAMP
        );"
    ];

        foreach ($queries as $q) {
            $db->exec($q);
        }

        $db->exec("CREATE INDEX IF NOT EXISTS idx_login_attempts_ip ON login_attempts(ip_address, attempt_time);");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_rate_limits_ip ON rate_limits(ip_address, endpoint, request_time);");
        $db->exec("CREATE INDEX IF NOT EXISTS idx_security_logs_created ON security_logs(created_at);");
    }

    // Ensure columns exist if migrated from older version
    ensureColumnExists($db, 'clients', 'contact_person', 'TEXT');
    ensureColumnExists($db, 'invoices', 'paid_amount', 'REAL DEFAULT 0');
    ensureColumnExists($db, 'invoices', 'remaining_balance', 'REAL DEFAULT 0');
    ensureColumnExists($db, 'invoices', 'payment_term', "TEXT DEFAULT 'full'");
    ensureColumnExists($db, 'invoices', 'installment_months', "INTEGER DEFAULT 1");
    ensureColumnExists($db, 'invoices', 'monthly_payment', "REAL DEFAULT 0");
    ensureColumnExists($db, 'invoices', 'partner_assigned', "TEXT DEFAULT 'both'");
    ensureColumnExists($db, 'invoices', 'ibraheem_share', "REAL DEFAULT 0");
    ensureColumnExists($db, 'invoices', 'fawas_share', "REAL DEFAULT 0");
    ensureColumnExists($db, 'invoices', 'price_note', 'TEXT');
    ensureColumnExists($db, 'invoices', 'amount_due_note', 'TEXT');
    ensureColumnExists($db, 'invoices', 'package_includes', 'TEXT');
    ensureColumnExists($db, 'invoices', 'submitted_by', "TEXT DEFAULT 'admin'");
    ensureColumnExists($db, 'invoices', 'payment_receipt', "VARCHAR(255) DEFAULT NULL");

    // Default Settings for YARAMAY Computer Maintenance Services
    $defaultSettings = [
        'company_name'       => 'YARAMAY Computer Maintenance Services',
        'company_cr'         => 'Cambridge C5 -1G',
        'company_vat'        => 'VAT-PH-09617190588',
        'company_phone'      => '+639075251376 (SMS) / +966508624264',
        'company_email'      => 'billing@yaramay.online',
        'company_address'    => '#19 Street Brgy. San Andres Cainta Rizal Cambridge C5 -1G',
        'company_manager'    => 'Ferdenand (Ibraheem) Sabado',
        'company_title'      => 'General Manager',
        'company_cert'       => 'MCP, MCSE, MCSA and CISCO Certified',
        'company_website'    => 'www.yaramay.online',
        'company_gcash'      => '09617190588',
        'gcash_account_name' => 'Ferdenand Sabado',
        'partner_1_name'     => 'Mr. Ibraheem',
        'partner_2_name'     => 'Mr. Fawas',
        'company_stc'        => 'STC Pay / GCash',
        'company_logo'       => 'assets/images/yaramay-logo.png',
        'currency'           => 'PHP',
        'currency_symbol'    => '₱',
        'default_tax_rate'   => '0',
        'invoice_prefix'     => 'INV-',
        'invoice_footer'     => "We are open for your suggestions on improving our services with you.\nPayment can be transferred through STC Pay or GCash (Account Name: Ferdenand Sabado, GCash #: 09617190588).\nThanking you for doing Business with us!"
    ];

    $checkStmt = $db->prepare("SELECT COUNT(*) as count FROM settings WHERE `key` = ?");
    $insertStmt = $db->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?)");

    foreach ($defaultSettings as $k => $v) {
        $checkStmt->execute([$k]);
        if ($checkStmt->fetchColumn() == 0) {
            $insertStmt->execute([$k, $v]);
        }
    }

    // Check if partner shares need calculation on existing invoices
    try {
        $db->exec("
            UPDATE invoices SET 
                partner_assigned = 'both',
                ibraheem_share = ROUND(paid_amount / 2, 2),
                fawas_share = ROUND(paid_amount / 2, 2)
            WHERE (partner_assigned = 'both' OR partner_assigned IS NULL OR partner_assigned = '') 
              AND (ibraheem_share = 0 OR ibraheem_share IS NULL)
              AND paid_amount > 0;
        ");
        $db->exec("
            UPDATE invoices SET 
                partner_assigned = 'ibraheem',
                ibraheem_share = paid_amount,
                fawas_share = 0
            WHERE partner_assigned = 'ibraheem'
              AND (ibraheem_share = 0 OR ibraheem_share IS NULL)
              AND paid_amount > 0;
        ");
        $db->exec("
            UPDATE invoices SET 
                partner_assigned = 'fawas',
                ibraheem_share = 0,
                fawas_share = paid_amount
            WHERE partner_assigned = 'fawas'
              AND (fawas_share = 0 OR fawas_share IS NULL)
              AND paid_amount > 0;
        ");
    } catch (Exception $e) {}

    // Check installment fields on existing invoices
    $db->exec("
        UPDATE invoices SET 
            payment_term = 'full',
            installment_months = 1,
            monthly_payment = grand_total
        WHERE payment_term IS NULL OR payment_term = ''
    ");
    $db->exec("
        UPDATE invoices SET 
            payment_term = 'custom',
            installment_months = 12,
            monthly_payment = 7500.00
        WHERE id = 1
    ");

    // Seed default superadmin ibraheem01 if no users exist
    $userCount = (int)$db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($userCount === 0) {
        $hashed = password_hash('123456789', PASSWORD_DEFAULT);
        $insertUser = $db->prepare("
            INSERT INTO users (username, password, full_name, role, email)
            VALUES (?, ?, ?, ?, ?)
        ");
        $insertUser->execute([
            'ibraheem01',
            $hashed,
            'Mr. Ibraheem (Superadmin)',
            'superadmin',
            'ibraheem@yaramay.online'
        ]);
    }
}

function ensureColumnExists(PDO $db, string $table, string $column, string $definition): void {
    try {
        $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver === 'sqlite') {
            $stmt = $db->query("PRAGMA table_info($table)");
            $cols = $stmt->fetchAll(PDO::FETCH_COLUMN, 1);
            if (!in_array($column, $cols)) {
                $db->exec("ALTER TABLE $table ADD COLUMN $column $definition;");
            }
        } else {
            $stmt = $db->prepare("SHOW COLUMNS FROM `$table` LIKE ?");
            $stmt->execute([$column]);
            if (!$stmt->fetch()) {
                $db->exec("ALTER TABLE `$table` ADD COLUMN `$column` $definition;");
            }
        }
    } catch (Exception $e) {
        // Column check or table check error
    }
}

// Auto initialize schema
initDatabaseSchema();

// Load Web Application Firewall & Advanced Security Shield
require_once __DIR__ . '/security.php';

// Authentication Helpers
function isLoggedIn(): bool {
    return !empty($_SESSION['admin_user']) && !empty($_SESSION['admin_user']['id']);
}

function getCurrentUser(): ?array {
    return $_SESSION['admin_user'] ?? null;
}

function requireAuth(): void {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function isSuperAdmin(): bool {
    return isset($_SESSION['admin_user']['role']) && $_SESSION['admin_user']['role'] === 'superadmin';
}

function jsonSuccess($data = [], $message = 'Success') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data'    => $data
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

function jsonError($message = 'An error occurred while processing request', $code = 400) {
    // Keep HTTP 200 so free-host reverse proxies (Kesug/InfinityFree) do not replace JSON with HTML error pages
    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'message' => $message,
        'code'    => $code
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
