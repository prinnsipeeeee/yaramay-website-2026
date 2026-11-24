<?php
/**
 * YARAMAY Computer Maintenance Services
 * Advanced Web Application Firewall (WAF) & Security Shield
 * 
 * Features:
 * - Real-time SQL Injection, XSS, RCE, and Path Traversal detection & blocking
 * - Anti-Brute-Force Login Protection (IP Throttling & Temporary Lockout)
 * - Anti-DoS / Spam Rate Limiter
 * - Anti-Malware / Anti-Webshell File Upload Scanning
 * - Cryptographic CSRF Protection
 * - Security Headers (Clickjacking, MIME-Sniffing, XSS Filtering, CSP)
 * - Persistent Security Incident & Audit Logging
 */

// 1. Emit Secure HTTP Response Headers
function applySecurityHeaders(): void {
    if (headers_sent()) return;

    // Prevent clickjacking
    header('X-Frame-Options: SAMEORIGIN');
    // Prevent MIME-type sniffing
    header('X-Content-Type-Options: nosniff');
    // Enable browser XSS protection
    header('X-XSS-Protection: 1; mode=block');
    // Strict Referrer Policy
    header('Referrer-Policy: strict-origin-when-cross-origin');
    // Remove server signature if possible
    @header_remove('X-Powered-By');

    // Permissions Policy
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

    // Content Security Policy
    $csp = "default-src 'self'; " .
           "script-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
           "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
           "font-src 'self' https://fonts.gstatic.com data:; " .
           "img-src 'self' data: https: blob:; " .
           "connect-src 'self' *; " .
           "object-src 'none'; " .
           "base-uri 'self'; " .
           "frame-ancestors 'self';";
    header("Content-Security-Policy: " . $csp);
}

// 2. Get Client IP Address Safely
function getClientIP(): string {
    $headers = [
        'HTTP_CF_CONNECTING_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_REAL_IP',
        'REMOTE_ADDR'
    ];
    foreach ($headers as $h) {
        if (!empty($_SERVER[$h])) {
            $ipList = explode(',', $_SERVER[$h]);
            $ip = trim($ipList[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
}

// 3. Security Audit Logging
function logSecurityEvent(string $eventType, string $details, ?string $username = null): void {
    try {
        $db = getDatabaseConnection();
        $ip = getClientIP();
        $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255);
        $now = date('Y-m-d H:i:s');

        $stmt = $db->prepare("
            INSERT INTO security_logs (event_type, ip_address, username, details, user_agent, created_at)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$eventType, $ip, $username, $details, $ua, $now]);
    } catch (Exception $e) {
        // Fallback to error_log if db is not ready
        error_log("[SECURITY EVENT] {$eventType} from IP " . getClientIP() . ": {$details}");
    }
}

// 4. Web Application Firewall (WAF) - Malicious Payload Detection
class SecurityShield {
    
    // Dangerous patterns signature library
    private static array $attackSignatures = [
        // SQL Injection signatures (targets malicious SQL injection vectors without blocking legitimate business text)
        'SQL_INJECTION' => [
            '/\b(union\s+(all\s+)?select)\b/i',
            '/\b(select\s+[\w\*,\s]+\s+from\s+(?:users|invoices|clients|settings|information_schema|mysql\.|sys\.))/i',
            '/\b(insert\s+into\s+.+\s+values)/i',
            '/\b(drop\s+(table|database))\b/i',
            '/\b(alter\s+table)\b/i',
            '/\b(exec(\s|\+)+(s|x)p\w+)/i',
            '/\b(benchmark\s*\(|sleep\s*\(|waitfor\s+delay)\b/i',
            '/\b(or\s+1\s*=\s*1|and\s+1\s*=\s*1|or\s+[\'"]1[\'"]\s*=\s*[\'"]1[\'"])\b/i',
            '/(?:[\'\"`]\s*(?:--|#|\/\*)|(?<!-)\/\*.*?\*\/)/',
            '/\b(information_schema|load_file|into\s+outfile)\b/i'
        ],
        // Cross-Site Scripting (XSS) signatures
        'XSS_ATTACK' => [
            '/<script\b[^>]*>(.*?)<\/script>/is',
            '/javascript:\s*[^\s]+/i',
            '/vbscript:\s*[^\s]+/i',
            '/data:text\/html/i',
            '/\bon(load|error|click|mouseover|mouseenter|focus|blur|submit)\s*=/i',
            '/<(iframe|embed|object|base|link|meta)\b/i'
        ],
        // Remote Code Execution & Command Injection signatures
        'RCE_ATTACK' => [
            '/(?:<\?php\b|;\s*|\bphp\s+-r\s+[\'"]).*?\bsystem\s*\(/is',
            '/\b(passthru|shell_exec|proc_open|popen)\s*\(/i',
            '/\b(eval|assert|base64_decode)\s*\(/i',
            '/\b(cmd\.exe|powershell\.exe|\/bin\/sh|\/bin\/bash)\b/i'
        ],
        // Path / Directory Traversal signatures
        'PATH_TRAVERSAL' => [
            '/\.\.\//',
            '/\.\.\\\\/',
            '/%2e%2e%2f/i',
            '/%2e%2e\//i',
            '/\b(etc\/passwd|win\.ini|boot\.ini)\b/i'
        ],
        // Null Byte injection
        'NULL_BYTE' => [
            '/\x00/',
            '/%00/'
        ]
    ];

    /**
     * Inspect all incoming request parameters
     */
    public static function inspectRequest(): void {
        // Skip CLI execution
        if (php_sapi_name() === 'cli') return;

        $targetSources = [
            'GET'    => $_GET,
            'POST'   => $_POST,
            'COOKIE' => $_COOKIE,
            'URI'    => [$_SERVER['REQUEST_URI'] ?? '']
        ];

        foreach ($targetSources as $sourceName => $data) {
            self::scanRecursive($data, $sourceName);
        }
    }

    private static function scanRecursive($input, string $sourceName): void {
        if (is_array($input)) {
            foreach ($input as $k => $v) {
                self::scanRecursive($k, $sourceName . '.key');
                self::scanRecursive($v, $sourceName . '.val');
            }
            return;
        }

        if (!is_string($input) || strlen($input) < 2) return;

        foreach (self::$attackSignatures as $attackType => $patterns) {
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $input)) {
                    self::blockAttack($attackType, $sourceName, $input);
                }
            }
        }
    }

    private static function blockAttack(string $attackType, string $source, string $payload): void {
        $ip = getClientIP();
        $safePayload = htmlspecialchars(substr($payload, 0, 120));
        $details = "Blocked {$attackType} detected in {$source}: {$safePayload}";

        logSecurityEvent('ATTACK_BLOCKED', $details);

        // Check if request is an API / AJAX / JSON request
        $isJson = (
            (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
            (!empty($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) ||
            (!empty($_SERVER['SCRIPT_NAME']) && basename($_SERVER['SCRIPT_NAME']) === 'api.php') ||
            (isset($_REQUEST['action']))
        );

        if ($isJson) {
            http_response_code(200);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => "Security check blocked request: {$attackType} pattern detected. Please avoid executable script tags or sql comment characters."
            ]);
            exit;
        }

        http_response_code(403);

        header('Content-Type: text/html; charset=utf-8');
        echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <title>403 Security Access Denied - YARAMAY Shield</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #0f172a; color: #f8fafc; padding: 40px 20px; text-align: center; }
        .box { max-width: 580px; margin: 40px auto; background: #1e293b; padding: 32px; border-radius: 12px; border: 1.5px solid #dc2626; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
        h1 { color: #dc2626; font-size: 24px; margin-bottom: 12px; }
        p { color: #94a3b8; font-size: 14px; line-height: 1.6; }
        .badge { background: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 6px; font-family: monospace; font-size: 13px; font-weight: 700; display: inline-block; margin-top: 10px; }
        .ip-info { margin-top: 20px; font-size: 12px; color: #64748b; }
    </style>
</head>
<body>
    <div class='box'>
        <h1>403 Forbidden - Security Shield Block</h1>
        <p>A potential malicious attack payload was intercepted and blocked by the YARAMAY Security Firewall.</p>
        <div class='badge'>Threat Detected: " . htmlspecialchars($attackType) . "</div>
        <div class='ip-info'>Your IP Address: " . htmlspecialchars($ip) . " • Timestamp: " . date('Y-m-d H:i:s') . "</div>
    </div>
</body>
</html>";
        exit;
    }
}

// 5. Anti-Brute-Force & Failed Login Throttling
class BruteForceProtector {
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_SECONDS = 900; // 15 minutes lockout

    public static function checkLockout(string $username): void {
        if (php_sapi_name() === 'cli') return;

        $db = getDatabaseConnection();
        $ip = getClientIP();
        $cutoff = date('Y-m-d H:i:s', time() - self::LOCKOUT_SECONDS);

        $stmt = $db->prepare("
            SELECT COUNT(*) FROM login_attempts 
            WHERE (ip_address = ? OR username = ?) 
              AND status = 'failed' 
              AND attempt_time > ?
        ");
        $stmt->execute([$ip, $username, $cutoff]);
        $fails = (int)$stmt->fetchColumn();

        if ($fails >= self::MAX_ATTEMPTS) {
            logSecurityEvent('BRUTE_FORCE_LOCKOUT', "IP or User locked out after {$fails} failed attempts.", $username);
            http_response_code(429);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => "Too many failed login attempts. Your account/IP has been temporarily locked for 15 minutes for security protection."
            ]);
            exit;
        }
    }

    public static function recordFailedAttempt(string $username): void {
        $db = getDatabaseConnection();
        $ip = getClientIP();
        $now = date('Y-m-d H:i:s');
        $stmt = $db->prepare("INSERT INTO login_attempts (ip_address, username, attempt_time, status) VALUES (?, ?, ?, 'failed')");
        $stmt->execute([$ip, $username, $now]);

        logSecurityEvent('LOGIN_FAILED', "Failed login attempt for username '{$username}'", $username);
    }

    public static function clearAttempts(string $username): void {
        $db = getDatabaseConnection();
        $ip = getClientIP();
        $stmt = $db->prepare("DELETE FROM login_attempts WHERE ip_address = ? OR username = ?");
        $stmt->execute([$ip, $username]);
    }
}

// 6. Anti-Spam / Rate Limiting on Public Endpoints
class RateLimiter {
    public static function checkRate(string $actionName, int $maxAllowed = 15, int $windowSeconds = 600): void {
        if (php_sapi_name() === 'cli') return;

        $db = getDatabaseConnection();
        $ip = getClientIP();
        $cutoff = date('Y-m-d H:i:s', time() - $windowSeconds);

        // Clean old rate limit entries
        $cleanStmt = $db->prepare("DELETE FROM rate_limits WHERE request_time < ?");
        $cleanStmt->execute([$cutoff]);

        // Count recent requests
        $stmt = $db->prepare("
            SELECT COUNT(*) FROM rate_limits 
            WHERE ip_address = ? AND endpoint = ? AND request_time > ?
        ");
        $stmt->execute([$ip, $actionName, $cutoff]);
        $count = (int)$stmt->fetchColumn();

        if ($count >= $maxAllowed) {
            logSecurityEvent('RATE_LIMITED', "Exceeded request limit for {$actionName} ({$count}/{$maxAllowed})");
            http_response_code(429);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => "Request rate limit exceeded. Please wait a few minutes before submitting again."
            ]);
            exit;
        }

        // Record request
        $now = date('Y-m-d H:i:s');
        $insert = $db->prepare("INSERT INTO rate_limits (ip_address, endpoint, request_time) VALUES (?, ?, ?)");
        $insert->execute([$ip, $actionName, $now]);
    }
}

// 7. Cryptographic CSRF Token Protection
class CSRFShield {
    public static function getToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verify(?string $token): bool {
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function requireValidToken(): void {
        if (php_sapi_name() === 'cli') return;

        // Extract token from header or post body
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!self::verify($token)) {
            logSecurityEvent('CSRF_FAILED', 'Invalid or missing CSRF security token');
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => 'Security Error: Invalid or expired CSRF token. Please refresh the page.'
            ]);
            exit;
        }
    }
}

// 8. Anti-Malware File Upload Inspector
class UploadInspector {
    public static function scanUploadedFile(array $file, array $allowedExtensions = ['json'], int $maxSizeBytes = 5242880): array {
        if (empty($file) || !isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return ['valid' => false, 'error' => 'No file was uploaded.'];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['valid' => false, 'error' => 'File upload error code: ' . $file['error']];
        }

        // Size check
        if ($file['size'] > $maxSizeBytes) {
            return ['valid' => false, 'error' => 'File exceeds maximum allowed size of 5MB.'];
        }

        // Extension check
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExtensions)) {
            logSecurityEvent('MALWARE_REJECTED', "Rejected file with unauthorized extension: {$file['name']}");
            return ['valid' => false, 'error' => "Unauthorized file extension '.{$ext}'. Only " . implode(', ', $allowedExtensions) . " allowed."];
        }

        // Inspect file content for malicious executable signatures
        $content = file_get_contents($file['tmp_name']);
        if ($content === false) {
            return ['valid' => false, 'error' => 'Unable to inspect uploaded file.'];
        }

        $maliciousSignatures = [
            '/<\?php/i',
            '/<\?=/i',
            '/<script\b/i',
            '/\beval\s*\(/i',
            '/\bbase64_decode\s*\(/i',
            '/\bpassthru\s*\(/i',
            '/\bshell_exec\s*\(/i',
            '/GIF89a.*<\?/s' // image polyglot shell
        ];

        // Null byte check applies to text/JSON files only, not binary images
        if ($ext === 'json') {
            $maliciousSignatures[] = '/\x00/';
        }

        foreach ($maliciousSignatures as $sig) {
            if (preg_match($sig, $content)) {
                logSecurityEvent('MALWARE_REJECTED', "Malicious code detected in uploaded file: {$file['name']}");
                return ['valid' => false, 'error' => 'Security Threat Detected: File contains unauthorized code or executable scripts.'];
            }
        }

        // Validate image files with getimagesize
        if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'gif'])) {
            $imgInfo = @getimagesize($file['tmp_name']);
            if ($imgInfo === false) {
                return ['valid' => false, 'error' => 'Uploaded file is not a valid image format.'];
            }
        }

        // Must parse as valid JSON for backup files
        if ($ext === 'json') {
            $json = json_decode($content, true);
            if (!is_array($json)) {
                return ['valid' => false, 'error' => 'File is corrupted or not a valid JSON document.'];
            }
        }

        return ['valid' => true, 'content' => $content];
    }
}

// Run automatic defenses on load
applySecurityHeaders();
SecurityShield::inspectRequest();
