<?php
/**
 * YARAMAY Computer Maintenance Services
 * Admin Dashboard Login Portal
 */

require_once __DIR__ . '/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$username = '';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $csrfToken = $_POST['csrf_token'] ?? '';

    // 1. Verify CSRF Token
    if (!CSRFShield::verify($csrfToken)) {
        $error = 'Security session expired or invalid token. Please refresh and try again.';
    } elseif (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        // 2. Check Brute-Force lockout
        BruteForceProtector::checkLockout($username);

        $db = getDatabaseConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user) {
            $valid = false;
            if (password_verify($password, $user['password'])) {
                $valid = true;
            } elseif ($password === $user['password']) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $db->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$newHash, $user['id']]);
                $valid = true;
            }

            if ($valid) {
                // Successful login: clear any failed attempts and regenerate session ID
                BruteForceProtector::clearAttempts($username);
                session_regenerate_id(true);

                $_SESSION['admin_user'] = [
                    'id'        => (int)$user['id'],
                    'username'  => $user['username'],
                    'full_name' => $user['full_name'],
                    'role'      => $user['role'],
                    'email'     => $user['email']
                ];

                logSecurityEvent('LOGIN_SUCCESS', 'Successful authentication', $username);
                header('Location: index.php');
                exit;
            } else {
                BruteForceProtector::recordFailedAttempt($username);
                $error = 'Invalid password. Please try again.';
            }
        } else {
            BruteForceProtector::recordFailedAttempt($username);
            $error = 'User not found with username "' . htmlspecialchars($username) . '".';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - YARAMAY Computer Maintenance Services</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/png" href="assets/images/favicon.png">

    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #172554 100%);
            padding: 20px;
            font-family: 'Inter', sans-serif;
        }

        .login-card {
            background: #ffffff;
            width: 100%;
            max-width: 440px;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .login-header {
            background: linear-gradient(180deg, #172554 0%, #1e3a8a 100%);
            padding: 32px 24px 24px 24px;
            text-align: center;
            color: #ffffff;
            position: relative;
        }

        .login-header .logo-svg {
            margin-bottom: 12px;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.2));
        }

        .login-header h1 {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 2px;
            margin-bottom: 4px;
        }

        .login-header p {
            font-size: 12px;
            color: #bfdbfe;
            font-weight: 500;
        }

        .login-body {
            padding: 28px 24px;
        }

        .login-alert-error {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 6px;
        }

        .form-input {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            color: #0f172a;
            transition: all 0.2s ease;
            outline: none;
            box-sizing: border-box;
        }

        .form-input:focus {
            border-color: #1e3a8a;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.15);
        }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: #1e3a8a;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 8px;
        }

        .btn-login:hover {
            background: #172554;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.35);
        }

        .credentials-hint {
            margin-top: 22px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            padding: 12px;
            border-radius: 8px;
            font-size: 12px;
            color: #475569;
        }

        .credentials-hint strong {
            color: #0f172a;
        }

        .portal-link-box {
            margin-top: 20px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 16px;
        }

        .portal-link-box a {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #dc2626;
            font-weight: 700;
            font-size: 13px;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .portal-link-box a:hover {
            color: #b91c1c;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <div style="background:#ffffff; border-radius:12px; padding:10px 16px; display:inline-flex; align-items:center; justify-content:center; margin-bottom:12px; box-shadow:0 4px 14px rgba(0,0,0,0.2);">
            <img src="assets/images/yaramay-logo.png" alt="YARAMAY Computer Maintenance Services" style="height:62px; width:auto; display:block;">
        </div>
        <p style="font-size:12px; color:#bfdbfe; font-weight:600; margin:0; letter-spacing:0.5px;">Computer Maintenance Services • Admin Portal</p>
    </div>

    <div class="login-body">
        <?php if (!empty($error)): ?>
            <div class="login-alert-error">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <input type="hidden" name="csrf_token" value="<?= CSRFShield::getToken() ?>">
            <div class="form-group">
                <label for="username">Admin Username</label>
                <input type="text" id="username" name="username" class="form-input" placeholder="e.g. ibraheem01" value="<?= htmlspecialchars($username) ?>" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-input" placeholder="Enter your password" required>
            </div>

            <button type="submit" class="btn-login">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                Sign In to Admin Dashboard
            </button>
        </form>

        <div class="credentials-hint">
            <div style="font-weight:700; margin-bottom:4px; color:#1e3a8a;">Default Superadmin Credentials:</div>
            
            <div style="font-size:11px; color:#64748b; margin-top:4px;">(Password and username can be edited inside the dashboard)</div>
        </div>

        <div class="portal-link-box">
            <span style="font-size:12px; color:#64748b; display:block; margin-bottom:4px;">Are you a client submitting a payment or invoice?</span>
            <a href="portal.php">
                Go to Dedicated Client Invoice Portal &rarr;
            </a>
        </div>
    </div>
</div>

</body>
</html>
