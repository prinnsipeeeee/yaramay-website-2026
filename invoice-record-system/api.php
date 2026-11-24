<?php
/**
 * YARAMAY Computer Maintenance Services
 * Invoice Record System API
 * Includes Partner Share Division (Mr. Ibraheem / Mr. Fawas / Both)
 * and Installment Plan Calculations (Full / 3 Months / 6 Months / Custom)
 */

require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$db = getDatabaseConnection();

// Read JSON input if sent as application/json
$rawInput = file_get_contents('php://input');
$jsonInput = json_decode($rawInput, true);
if (is_array($jsonInput)) {
    $_POST = array_merge($_POST, $jsonInput);
    if (empty($action) && isset($jsonInput['action'])) {
        $action = $jsonInput['action'];
    }
}

// Authentication gate: require login for admin endpoints
$isCli = (php_sapi_name() === 'cli');
$publicActions = ['login', 'logout', 'get_current_user', 'submit_client_invoice', 'get_settings'];
if (!$isCli && !in_array($action, $publicActions) && !isLoggedIn()) {
    jsonError('Unauthorized. Please log in to access the admin dashboard.', 401);
}

try {
    switch ($action) {

        // ==========================================
        // Authentication & Session
        // ==========================================
        case 'login':
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($username) || empty($password)) {
                jsonError('Please enter both username and password.');
            }

            // Anti-Brute-Force Check
            BruteForceProtector::checkLockout($username);

            $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if (!$user) {
                BruteForceProtector::recordFailedAttempt($username);
                jsonError('Invalid username or password.');
            }

            $passwordValid = false;
            if (password_verify($password, $user['password'])) {
                $passwordValid = true;
            } elseif ($password === $user['password']) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $db->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$newHash, $user['id']]);
                $passwordValid = true;
            }

            if (!$passwordValid) {
                BruteForceProtector::recordFailedAttempt($username);
                jsonError('Invalid username or password.');
            }

            // Authentication succeeded: clear brute force counters & regenerate session ID
            BruteForceProtector::clearAttempts($username);
            session_regenerate_id(true);

            $_SESSION['admin_user'] = [
                'id'        => (int)$user['id'],
                'username'  => $user['username'],
                'full_name' => $user['full_name'],
                'role'      => $user['role'],
                'email'     => $user['email']
            ];

            logSecurityEvent('LOGIN_SUCCESS', 'Successful API login', $username);

            jsonSuccess([
                'user' => $_SESSION['admin_user']
            ], 'Welcome back, ' . $user['full_name']);
            break;

        case 'logout':
            $_SESSION['admin_user'] = null;
            unset($_SESSION['admin_user']);
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_regenerate_id(true);
            }
            jsonSuccess(null, 'Logged out successfully.');
            break;

        case 'get_current_user':
            if (isLoggedIn()) {
                jsonSuccess($_SESSION['admin_user']);
            } else {
                jsonSuccess(null);
            }
            break;

        // ==========================================
        // Admin Users Management
        // ==========================================
        case 'get_users':
            $stmt = $db->query("SELECT id, username, full_name, role, email, created_at, updated_at FROM users ORDER BY id ASC");
            $users = $stmt->fetchAll();
            jsonSuccess($users);
            break;

        case 'save_user':
            $id = (int)($_POST['id'] ?? 0);
            $username = trim($_POST['username'] ?? '');
            $fullName = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $role = trim($_POST['role'] ?? 'admin');
            $password = trim($_POST['password'] ?? '');

            if (!in_array($role, ['superadmin', 'admin'])) {
                $role = 'admin';
            }

            if (empty($username) || empty($fullName)) {
                jsonError('Username and Full Name are required.');
            }

            // Check if username already exists for another account
            $checkStmt = $db->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
            $checkStmt->execute([$username, $id]);
            if ($checkStmt->fetch()) {
                jsonError("Username '{$username}' is already in use by another account.");
            }

            if ($id === 0) {
                if (empty($password)) {
                    jsonError('Password is required for new admin users.');
                }
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $now = date('Y-m-d H:i:s');
                $stmt = $db->prepare("
                    INSERT INTO users (username, password, full_name, role, email, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$username, $hashed, $fullName, $role, $email, $now, $now]);
                $newId = (int)$db->lastInsertId();
                jsonSuccess(['id' => $newId], 'Admin user created successfully.');
            } else {
                $currentUser = $db->prepare("SELECT * FROM users WHERE id = ?");
                $currentUser->execute([$id]);
                $existing = $currentUser->fetch();
                if (!$existing) {
                    jsonError('User not found.');
                }

                $now = date('Y-m-d H:i:s');
                if (!empty($password)) {
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $db->prepare("
                        UPDATE users SET username = ?, full_name = ?, role = ?, email = ?, password = ?, updated_at = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$username, $fullName, $role, $email, $hashed, $now, $id]);
                } else {
                    $stmt = $db->prepare("
                        UPDATE users SET username = ?, full_name = ?, role = ?, email = ?, updated_at = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$username, $fullName, $role, $email, $now, $id]);
                }

                // If currently logged-in user updated their own info, refresh session
                if (isset($_SESSION['admin_user']) && (int)$_SESSION['admin_user']['id'] === $id) {
                    $_SESSION['admin_user']['username'] = $username;
                    $_SESSION['admin_user']['full_name'] = $fullName;
                    $_SESSION['admin_user']['role'] = $role;
                    $_SESSION['admin_user']['email'] = $email;
                }

                jsonSuccess(['id' => $id], 'Admin user updated successfully.');
            }
            break;

        case 'delete_user':
            $id = (int)($_POST['id'] ?? 0);
            if (!$id) jsonError('User ID is required.');

            if (isset($_SESSION['admin_user']) && (int)$_SESSION['admin_user']['id'] === $id) {
                jsonError('You cannot delete your own currently active account.');
            }

            // Check if this is the last superadmin
            $userToDelete = $db->prepare("SELECT role FROM users WHERE id = ?");
            $userToDelete->execute([$id]);
            $roleToDelete = $userToDelete->fetchColumn();

            if ($roleToDelete === 'superadmin') {
                $superAdminCount = (int)$db->query("SELECT COUNT(*) FROM users WHERE role = 'superadmin' AND id != {$id}")->fetchColumn();
                if ($superAdminCount < 1) {
                    jsonError('Cannot delete the last remaining Superadmin account.');
                }
            }

            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            jsonSuccess(null, 'Admin user deleted successfully.');
            break;

        case 'change_my_password':
            if (!isLoggedIn()) jsonError('Unauthorized', 401);
            $userId = (int)$_SESSION['admin_user']['id'];
            $currentPassword = trim($_POST['current_password'] ?? '');
            $newPassword = trim($_POST['new_password'] ?? '');

            if (empty($newPassword) || strlen($newPassword) < 6) {
                jsonError('New password must be at least 6 characters long.');
            }

            $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $userHash = $stmt->fetchColumn();

            if (!password_verify($currentPassword, $userHash) && $currentPassword !== $userHash) {
                jsonError('Current password is incorrect.');
            }

            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $now = date('Y-m-d H:i:s');
            $db->prepare("UPDATE users SET password = ?, updated_at = ? WHERE id = ?")->execute([$newHash, $now, $userId]);
            jsonSuccess(null, 'Password changed successfully!');
            break;

        // ==========================================
        // 1. Dashboard Statistics
        // ==========================================
        case 'get_dashboard_stats':
            $totalSales = (float)$db->query("SELECT COALESCE(SUM(grand_total), 0) FROM invoices WHERE status != 'cancelled'")->fetchColumn();
            $paidTotal = (float)$db->query("SELECT COALESCE(SUM(paid_amount), 0) FROM invoices WHERE status != 'cancelled'")->fetchColumn();
            $balanceTotal = (float)$db->query("SELECT COALESCE(SUM(remaining_balance), 0) FROM invoices WHERE status != 'cancelled'")->fetchColumn();
            
            // Partner Shares Division (Mr. Ibraheem & Mr. Fawas)
            // Dynamically calculate from paid_amount so partner accounts NEVER show 0.00 if payments exist!
            $sharesStmt = $db->query("
                SELECT 
                    COALESCE(SUM(
                        CASE 
                            WHEN partner_assigned = 'ibraheem' THEN paid_amount
                            WHEN partner_assigned = 'fawas' THEN 0
                            ELSE ROUND(paid_amount / 2, 2)
                        END
                    ), 0) as ibraheem_total,
                    COALESCE(SUM(
                        CASE 
                            WHEN partner_assigned = 'fawas' THEN paid_amount
                            WHEN partner_assigned = 'ibraheem' THEN 0
                            ELSE ROUND(paid_amount / 2, 2)
                        END
                    ), 0) as fawas_total
                FROM invoices 
                WHERE status != 'cancelled'
            ");
            $sharesRow = $sharesStmt->fetch();
            $ibraheemTotal = (float)($sharesRow['ibraheem_total'] ?? 0);
            $fawasTotal = (float)($sharesRow['fawas_total'] ?? 0);

            $invoiceCount = (int)$db->query("SELECT COUNT(*) FROM invoices")->fetchColumn();
            $clientCount = (int)$db->query("SELECT COUNT(*) FROM clients")->fetchColumn();
            $productCount = (int)$db->query("SELECT COUNT(*) FROM products")->fetchColumn();

            // Status counts
            $statusCounts = [
                'paid' => (int)$db->query("SELECT COUNT(*) FROM invoices WHERE status = 'paid'")->fetchColumn(),
                'partial' => (int)$db->query("SELECT COUNT(*) FROM invoices WHERE status = 'partial'")->fetchColumn(),
                'pending' => (int)$db->query("SELECT COUNT(*) FROM invoices WHERE status = 'pending'")->fetchColumn(),
                'unpaid' => (int)$db->query("SELECT COUNT(*) FROM invoices WHERE status = 'unpaid'")->fetchColumn(),
                'cancelled' => (int)$db->query("SELECT COUNT(*) FROM invoices WHERE status = 'cancelled'")->fetchColumn(),
            ];

            // Partner count
            $partnerCounts = [
                'both' => (int)$db->query("SELECT COUNT(*) FROM invoices WHERE partner_assigned = 'both'")->fetchColumn(),
                'ibraheem' => (int)$db->query("SELECT COUNT(*) FROM invoices WHERE partner_assigned = 'ibraheem'")->fetchColumn(),
                'fawas' => (int)$db->query("SELECT COUNT(*) FROM invoices WHERE partner_assigned = 'fawas'")->fetchColumn(),
            ];

            // Recent invoices
            $recentStmt = $db->query("
                SELECT i.*, 
                       c.name as client_name, 
                       c.contact_person, 
                       c.phone as client_phone 
                FROM invoices i
                LEFT JOIN clients c ON i.client_id = c.id
                ORDER BY i.issue_date DESC, i.id DESC 
                LIMIT 8
            ");
            $recentInvoices = $recentStmt->fetchAll();

            // Client financial summary for Admin Dashboard - fully compatible with MySQL ONLY_FULL_GROUP_BY & SQLite
            $clientSummaryStmt = $db->query("
                SELECT c.id, c.name, c.contact_person, c.phone, c.address,
                       COUNT(i.id) as invoice_count,
                       COALESCE(SUM(CASE WHEN i.status != 'cancelled' THEN i.grand_total ELSE 0 END), 0) as total_price,
                       COALESCE(SUM(CASE WHEN i.status != 'cancelled' THEN i.paid_amount ELSE 0 END), 0) as total_paid,
                       COALESCE(SUM(CASE WHEN i.status != 'cancelled' THEN i.remaining_balance ELSE 0 END), 0) as remaining_balance
                FROM clients c
                LEFT JOIN invoices i ON c.id = i.client_id
                GROUP BY c.id, c.name, c.contact_person, c.phone, c.address
                ORDER BY remaining_balance DESC, total_price DESC
            ");
            $clientsSummary = $clientSummaryStmt->fetchAll();

            jsonSuccess([
                'total_sales'             => $totalSales,
                'paid_total'              => $paidTotal,
                'remaining_balance_total' => $balanceTotal,
                'ibraheem_total'          => $ibraheemTotal,
                'fawas_total'             => $fawasTotal,
                'invoice_count'           => $invoiceCount,
                'client_count'            => $clientCount,
                'product_count'           => $productCount,
                'status_counts'           => $statusCounts,
                'partner_counts'          => $partnerCounts,
                'recent_invoices'         => $recentInvoices,
                'clients_summary'         => $clientsSummary
            ]);
            break;

        // ==========================================
        // 2. Invoices List with Search & Filter
        // ==========================================
        case 'get_invoices':
            $status = trim($_GET['status'] ?? '');
            $partner = trim($_GET['partner'] ?? '');
            $search = trim($_GET['search'] ?? '');
            $fromDate = trim($_GET['from_date'] ?? '');
            $toDate = trim($_GET['to_date'] ?? '');
            $clientId = (int)($_GET['client_id'] ?? 0);

            $sql = "
                SELECT i.*, 
                       c.name as client_name, 
                       c.contact_person,
                       c.phone as client_phone,
                       c.address as client_address,
                       c.tax_number as client_tax,
                       (SELECT COUNT(*) FROM invoice_items ii WHERE ii.invoice_id = i.id) as items_count
                FROM invoices i
                LEFT JOIN clients c ON i.client_id = c.id
                WHERE 1=1
            ";
            $params = [];

            if (!empty($status) && $status !== 'all') {
                $sql .= " AND i.status = ?";
                $params[] = $status;
            }

            if (!empty($partner) && $partner !== 'all') {
                $sql .= " AND i.partner_assigned = ?";
                $params[] = $partner;
            }

            if ($clientId > 0) {
                $sql .= " AND i.client_id = ?";
                $params[] = $clientId;
            }

            if (!empty($search)) {
                $sql .= " AND (i.invoice_number LIKE ? OR c.name LIKE ? OR c.contact_person LIKE ? OR c.phone LIKE ?)";
                $wildcard = "%$search%";
                $params[] = $wildcard;
                $params[] = $wildcard;
                $params[] = $wildcard;
                $params[] = $wildcard;
            }

            if (!empty($fromDate)) {
                $sql .= " AND i.issue_date >= ?";
                $params[] = $fromDate;
            }

            if (!empty($toDate)) {
                $sql .= " AND i.issue_date <= ?";
                $params[] = $toDate;
            }

            $sql .= " ORDER BY i.issue_date DESC, i.id DESC";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $invoices = $stmt->fetchAll();

            jsonSuccess($invoices);
            break;

        // ==========================================
        // 3. Single Invoice Details
        // ==========================================
        case 'get_invoice':
            $id = (int)($_GET['id'] ?? 0);
            if (!$id) jsonError('Invoice ID is required');

            $stmt = $db->prepare("
                SELECT i.*, 
                       c.name as client_name, 
                       c.contact_person,
                       c.phone as client_phone, 
                       c.email as client_email, 
                       c.address as client_address, 
                       c.tax_number as client_tax
                FROM invoices i
                LEFT JOIN clients c ON i.client_id = c.id
                WHERE i.id = ?
            ");
            $stmt->execute([$id]);
            $invoice = $stmt->fetch();

            if (!$invoice) jsonError('Invoice not found', 404);

            // Clean date formats (YYYY-MM-DD) for HTML5 date inputs
            if (!empty($invoice['issue_date'])) {
                $invoice['issue_date'] = substr($invoice['issue_date'], 0, 10);
            }
            if (!empty($invoice['due_date'])) {
                $invoice['due_date'] = substr($invoice['due_date'], 0, 10);
            }

            // Fetch items
            $itemStmt = $db->prepare("SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY id ASC");
            $itemStmt->execute([$id]);
            $invoice['items'] = $itemStmt->fetchAll() ?: [];

            // Settings
            $settingsList = $db->query("SELECT `key`, value FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
            $invoice['settings'] = $settingsList ?: [];

            jsonSuccess($invoice);
            break;

        // ==========================================
        // 4. Save Invoice (Create or Update)
        // ==========================================
        case 'save_invoice':
            $id = (int)($_POST['id'] ?? 0);
            $invoiceNumber = trim($_POST['invoice_number'] ?? '');
            $clientId = !empty($_POST['client_id']) ? (int)$_POST['client_id'] : null;
            $newClientName = trim($_POST['new_client_name'] ?? '');
            $newContactPerson = trim($_POST['new_contact_person'] ?? '');
            $newClientPhone = trim($_POST['new_client_phone'] ?? '');
            $newClientAddress = trim($_POST['new_client_address'] ?? '');
            $issueDate = trim($_POST['issue_date'] ?? date('Y-m-d'));
            $dueDate = trim($_POST['due_date'] ?? date('Y-m-d', strtotime('+15 days')));
            $status = trim($_POST['status'] ?? 'unpaid');
            $paymentMethod = trim($_POST['payment_method'] ?? 'gcash');
            
            // Payment term & installment plan
            $paymentTerm = trim($_POST['payment_term'] ?? 'full'); // full, 3_months, 6_months, custom
            $installmentMonths = max(1, (int)($_POST['installment_months'] ?? 1));
            if ($paymentTerm === '3_months') $installmentMonths = 3;
            if ($paymentTerm === '6_months') $installmentMonths = 6;
            
            // Partner assignment (ibraheem, fawas, both)
            $partnerAssigned = trim($_POST['partner_assigned'] ?? 'both');
            if (!in_array($partnerAssigned, ['ibraheem', 'fawas', 'both'])) {
                $partnerAssigned = 'both';
            }

            $priceNote = trim($_POST['price_note'] ?? '');
            $amountDueNote = trim($_POST['amount_due_note'] ?? '');
            $notes = trim($_POST['notes'] ?? '');
            $items = $_POST['items'] ?? [];

            // Paid amount & Remaining balance
            $paidAmount = isset($_POST['paid_amount']) ? max(0, (float)$_POST['paid_amount']) : 0.0;
            $hasManualBalance = isset($_POST['remaining_balance']) && $_POST['remaining_balance'] !== '';
            $manualBalance = $hasManualBalance ? (float)$_POST['remaining_balance'] : null;

            if (empty($items) || !is_array($items)) {
                jsonError('Invoice must contain at least one line item');
            }

            // Quick create client if requested
            if (!$clientId && !empty($newClientName)) {
                $cStmt = $db->prepare("INSERT INTO clients (name, contact_person, phone, address) VALUES (?, ?, ?, ?)");
                $cStmt->execute([$newClientName, $newContactPerson, $newClientPhone, $newClientAddress]);
                $clientId = (int)$db->lastInsertId();
            }

            // Auto-generate invoice number if empty
            if (empty($invoiceNumber)) {
                $nextSeq = (int)$db->query("SELECT MAX(id) FROM invoices")->fetchColumn() + 1;
                $invoiceNumber = sprintf('%05d', $nextSeq);
            }

            // Calculate totals accurately on server
            $calculatedSubtotal = 0;
            $calculatedDiscount = 0;
            $calculatedTax = 0;
            $normalizedItems = [];

            foreach ($items as $item) {
                $itemName = trim($item['item_name'] ?? '');
                if (empty($itemName)) continue;

                $desc = trim($item['description'] ?? '');
                $qty = max(0.001, (float)($item['quantity'] ?? 1));
                $price = max(0, (float)($item['unit_price'] ?? 0));
                $discount = max(0, (float)($item['discount'] ?? 0));
                $taxRate = max(0, (float)($item['tax_rate'] ?? 0));

                $lineSubtotal = $qty * $price;
                $lineDiscount = min($lineSubtotal, $discount);
                $taxableAmount = max(0, $lineSubtotal - $lineDiscount);
                $lineTax = ($taxableAmount * $taxRate) / 100;
                $lineTotal = $taxableAmount + $lineTax;

                $calculatedSubtotal += $lineSubtotal;
                $calculatedDiscount += $lineDiscount;
                $calculatedTax += $lineTax;

                $normalizedItems[] = [
                    'item_name'   => $itemName,
                    'description' => $desc,
                    'quantity'    => $qty,
                    'unit_price'  => $price,
                    'discount'    => $lineDiscount,
                    'tax_rate'    => $taxRate,
                    'total'       => round($lineTotal, 2)
                ];
            }

            if (empty($normalizedItems)) {
                jsonError('Please ensure item details are filled out correctly');
            }

            $calculatedGrandTotal = round($calculatedSubtotal - $calculatedDiscount + $calculatedTax, 2);

            // Compute monthly payment formula
            $monthlyPayment = round($calculatedGrandTotal / $installmentMonths, 2);

            // Calculate remaining balance
            if ($hasManualBalance) {
                $finalBalance = round($manualBalance, 2);
            } else {
                $finalBalance = max(0, round($calculatedGrandTotal - $paidAmount, 2));
            }

            // Compute partner shares based on paid amount
            if ($partnerAssigned === 'ibraheem') {
                $ibraheemShare = $paidAmount;
                $fawasShare = 0.00;
            } elseif ($partnerAssigned === 'fawas') {
                $ibraheemShare = 0.00;
                $fawasShare = $paidAmount;
            } else { // 'both'
                $ibraheemShare = round($paidAmount / 2, 2);
                $fawasShare = round($paidAmount / 2, 2);
            }

            // Auto-adjust status based on payments if status wasn't cancelled
            if ($status !== 'cancelled') {
                if ($finalBalance <= 0 && $paidAmount >= $calculatedGrandTotal && $calculatedGrandTotal > 0) {
                    $status = 'paid';
                } elseif ($paidAmount > 0 && $finalBalance > 0) {
                    $status = 'partial';
                }
            }

            // If price note is empty and installment plan chosen, auto generate formula text
            if (empty($priceNote) && $installmentMonths > 1) {
                $priceNote = "PHP " . number_format($calculatedGrandTotal, 2) . " / " . $installmentMonths . " Months = PHP " . number_format($monthlyPayment, 2) . " / Month";
            }

            $db->beginTransaction();

            if ($id > 0) {
                // Update existing invoice
                $stmt = $db->prepare("
                    UPDATE invoices SET 
                        invoice_number = ?, client_id = ?, issue_date = ?, due_date = ?, 
                        status = ?, payment_method = ?, payment_term = ?, installment_months = ?,
                        monthly_payment = ?, partner_assigned = ?, ibraheem_share = ?, fawas_share = ?,
                        subtotal = ?, discount = ?, tax_amount = ?, grand_total = ?, 
                        paid_amount = ?, remaining_balance = ?, price_note = ?, amount_due_note = ?, notes = ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $invoiceNumber, $clientId, $issueDate, $dueDate,
                    $status, $paymentMethod, $paymentTerm, $installmentMonths,
                    $monthlyPayment, $partnerAssigned, $ibraheemShare, $fawasShare,
                    $calculatedSubtotal, $calculatedDiscount, $calculatedTax, $calculatedGrandTotal, 
                    $paidAmount, $finalBalance, $priceNote, $amountDueNote, $notes, $id
                ]);

                // Delete existing items to replace with updated list
                $db->prepare("DELETE FROM invoice_items WHERE invoice_id = ?")->execute([$id]);
                $invoiceId = $id;
            } else {
                // Check uniqueness of invoice number
                $checkUniq = $db->prepare("SELECT id FROM invoices WHERE invoice_number = ?");
                $checkUniq->execute([$invoiceNumber]);
                if ($checkUniq->fetch()) {
                    $invoiceNumber .= '-' . rand(10, 99);
                }

                // Insert new invoice
                $stmt = $db->prepare("
                    INSERT INTO invoices (
                        invoice_number, client_id, issue_date, due_date, status, 
                        payment_method, payment_term, installment_months, monthly_payment,
                        partner_assigned, ibraheem_share, fawas_share,
                        subtotal, discount, tax_amount, grand_total,
                        paid_amount, remaining_balance, price_note, amount_due_note,
                        notes, submitted_by
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'admin')
                ");
                $stmt->execute([
                    $invoiceNumber, $clientId, $issueDate, $dueDate, $status,
                    $paymentMethod, $paymentTerm, $installmentMonths, $monthlyPayment,
                    $partnerAssigned, $ibraheemShare, $fawasShare,
                    $calculatedSubtotal, $calculatedDiscount, $calculatedTax, $calculatedGrandTotal,
                    $paidAmount, $finalBalance, $priceNote, $amountDueNote, $notes
                ]);
                $invoiceId = (int)$db->lastInsertId();
            }

            // Insert line items
            $itemInsert = $db->prepare("
                INSERT INTO invoice_items (invoice_id, item_name, description, quantity, unit_price, discount, tax_rate, total)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            foreach ($normalizedItems as $it) {
                $itemInsert->execute([
                    $invoiceId,
                    $it['item_name'],
                    $it['description'],
                    $it['quantity'],
                    $it['unit_price'],
                    $it['discount'],
                    $it['tax_rate'],
                    $it['total']
                ]);
            }

            $db->commit();

            jsonSuccess([
                'invoice_id'        => $invoiceId, 
                'invoice_number'    => $invoiceNumber,
                'grand_total'       => $calculatedGrandTotal,
                'paid_amount'       => $paidAmount,
                'remaining_balance' => $finalBalance,
                'monthly_payment'   => $monthlyPayment,
                'installment_months'=> $installmentMonths,
                'partner_assigned'  => $partnerAssigned,
                'ibraheem_share'    => $ibraheemShare,
                'fawas_share'       => $fawasShare
            ], 'Invoice saved successfully');
            break;

        // ==========================================
        // 5. Client Self Fill-Up Form Submission
        // ==========================================
        case 'submit_client_invoice':
            // Anti-Spam / Rate-Limiting Protection (Max 10 submissions per 10 mins per IP)
            RateLimiter::checkRate('submit_client_invoice', 10, 600);

            $clientName = strip_tags(trim($_POST['client_name'] ?? ''));
            $contactPerson = strip_tags(trim($_POST['contact_person'] ?? ''));
            $phone = strip_tags(trim($_POST['phone'] ?? ''));
            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $address = strip_tags(trim($_POST['address'] ?? ''));
            $serviceName = strip_tags(trim($_POST['service_name'] ?? 'E-Commerce & Web Maintenance Services'));
            $serviceDesc = trim($_POST['service_description'] ?? '');
            $price = max(0, (float)($_POST['price'] ?? $_POST['total_amount'] ?? 7500.00));
            $paymentMethod = trim($_POST['payment_method'] ?? 'gcash');
            if (!in_array($paymentMethod, ['gcash', 'cash'])) {
                $paymentMethod = 'gcash';
            }

            // Installment Plan
            $paymentTerm = trim($_POST['payment_term'] ?? 'full');
            $installmentMonths = max(1, (int)($_POST['installment_months'] ?? 1));
            if ($paymentTerm === '3_months') $installmentMonths = 3;
            if ($paymentTerm === '6_months') $installmentMonths = 6;
            $monthlyPayment = round($price / $installmentMonths, 2);

            // Partner Assignment
            $partnerAssigned = trim($_POST['partner_assigned'] ?? 'both');
            if (!in_array($partnerAssigned, ['ibraheem', 'fawas', 'both'])) {
                $partnerAssigned = 'both';
            }

            $notes = trim($_POST['notes'] ?? '');

            if (empty($clientName)) {
                jsonError('Company or Client name is required');
            }

            $db->beginTransaction();

            // Find or create client
            $findClient = $db->prepare("SELECT id FROM clients WHERE name = ? OR (phone != '' AND phone = ?)");
            $findClient->execute([$clientName, $phone]);
            $existingClientId = $findClient->fetchColumn();

            if ($existingClientId) {
                $clientId = (int)$existingClientId;
                // Update contact details if provided
                $updateC = $db->prepare("UPDATE clients SET contact_person = COALESCE(NULLIF(?, ''), contact_person), address = COALESCE(NULLIF(?, ''), address), email = COALESCE(NULLIF(?, ''), email) WHERE id = ?");
                $updateC->execute([$contactPerson, $address, $email, $clientId]);
            } else {
                $insertC = $db->prepare("INSERT INTO clients (name, contact_person, phone, email, address) VALUES (?, ?, ?, ?, ?)");
                $insertC->execute([$clientName, $contactPerson, $phone, $email, $address]);
                $clientId = (int)$db->lastInsertId();
            }

            // Generate invoice number
            $nextSeq = (int)$db->query("SELECT MAX(id) FROM invoices")->fetchColumn() + 1;
            $invoiceNumber = sprintf('%05d', $nextSeq);

            // Default package items if none provided
            if (empty($serviceDesc)) {
                $serviceDesc = "Package of E-Commerce Maintenance Services and Web maintenance including Rescue System .\nIncludes: 1. Web design / Web system, 2. Job post / Job Offer, 3 Deployment System (QMS), 4. Maintenance System, 5. Location Rescue system, 6. E-commerce site with SSL Security, 7. PRA Deployment, 8. FRA/SRA SYSTEM, 9. Block Listing for OFW, 10. Voucher System.\nHosting and domain, with SSL security\n--- Nothing Follows ---";
            }

            $formulaText = ($installmentMonths > 1) 
                ? "Installment Plan: PHP " . number_format($price, 2) . " ÷ " . $installmentMonths . " Months = PHP " . number_format($monthlyPayment, 2) . " / Month" 
                : "Full Payment: PHP " . number_format($price, 2);

            $partnerLabel = ($partnerAssigned === 'both') ? 'Both (Mr. Ibraheem & Mr. Fawas - 50/50 Split)' : (($partnerAssigned === 'ibraheem') ? 'Mr. Ibraheem' : 'Mr. Fawas');

            $clientNote = "Requested via Client Invoice Portal.\nPayment Method: " . strtoupper($paymentMethod) . " (GCash: Ferdenand Sabado - 09617190588)\nAssigned Account: " . $partnerLabel . "\n" . $formulaText . "\n" . $notes;

            $today = date('Y-m-d');
            $dueDate = date('Y-m-d', strtotime('+15 days'));

            // Handle GCash / Payment Screenshot Upload
            $receiptPath = null;
            if (isset($_FILES['payment_receipt']) && $_FILES['payment_receipt']['error'] !== UPLOAD_ERR_NO_FILE) {
                $scan = UploadInspector::scanUploadedFile($_FILES['payment_receipt'], ['png', 'jpg', 'jpeg', 'webp', 'pdf'], 8388608); // Max 8MB
                if (!$scan['valid']) {
                    jsonError('Payment Screenshot Error: ' . $scan['error']);
                }

                $ext = strtolower(pathinfo($_FILES['payment_receipt']['name'], PATHINFO_EXTENSION));
                $receiptDir = __DIR__ . '/uploads/receipts';
                if (!is_dir($receiptDir)) {
                    @mkdir($receiptDir, 0755, true);
                }

                $cleanFilename = 'gcash_receipt_' . date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                $destination = $receiptDir . '/' . $cleanFilename;

                if (move_uploaded_file($_FILES['payment_receipt']['tmp_name'], $destination)) {
                    $receiptPath = 'uploads/receipts/' . $cleanFilename;
                    $clientNote .= "\n[GCash Payment Proof Attached: " . $cleanFilename . "]";
                } else {
                    jsonError('Failed to save payment receipt upload. Please try again.');
                }
            }

            $insertInv = $db->prepare("
                INSERT INTO invoices (
                    invoice_number, client_id, issue_date, due_date, status, 
                    payment_method, payment_term, installment_months, monthly_payment,
                    partner_assigned, ibraheem_share, fawas_share,
                    subtotal, discount, tax_amount, grand_total,
                    paid_amount, remaining_balance, price_note, amount_due_note,
                    package_includes, notes, payment_receipt, submitted_by
                ) VALUES (?, ?, ?, ?, 'pending', ?, ?, ?, ?, ?, 0, 0, ?, 0, 0, ?, 0, ?, ?, ?, ?, ?, ?, 'client')
            ");
            $insertInv->execute([
                $invoiceNumber,
                $clientId,
                $today,
                $dueDate,
                $paymentMethod,
                $paymentTerm,
                $installmentMonths,
                $monthlyPayment,
                $partnerAssigned,
                $price,
                $price,
                $price, // Remaining balance = full price initially
                $formulaText,
                ($installmentMonths > 1) ? "Monthly: PHP " . number_format($monthlyPayment, 2) : "PHP " . number_format($price, 2),
                $serviceDesc,
                $clientNote,
                $receiptPath
            ]);
            $invoiceId = (int)$db->lastInsertId();

            // Insert line item
            $insertItem = $db->prepare("
                INSERT INTO invoice_items (invoice_id, item_name, description, quantity, unit_price, discount, tax_rate, total)
                VALUES (?, ?, ?, 1, ?, 0, 0, ?)
            ");
            $insertItem->execute([
                $invoiceId,
                $serviceName,
                $serviceDesc,
                $price,
                $price
            ]);

            $db->commit();

            jsonSuccess([
                'invoice_id'         => $invoiceId,
                'invoice_number'     => $invoiceNumber,
                'client_name'        => $clientName,
                'grand_total'        => $price,
                'remaining_balance'  => $price,
                'payment_term'       => $paymentTerm,
                'installment_months' => $installmentMonths,
                'monthly_payment'    => $monthlyPayment,
                'partner_assigned'   => $partnerAssigned,
                'payment_receipt'    => $receiptPath,
                'gcash_name'         => 'Ferdenand Sabado',
                'gcash_number'       => '09617190588'
            ], 'Invoice request submitted successfully! Your invoice number is ' . $invoiceNumber);
            break;

        // ==========================================
        // 6. Delete Invoice
        // ==========================================
        case 'delete_invoice':
            $id = (int)($_POST['id'] ?? 0);
            if (!$id) jsonError('Invoice ID is required');

            $db->beginTransaction();
            $db->prepare("DELETE FROM invoice_items WHERE invoice_id = ?")->execute([$id]);
            $db->prepare("DELETE FROM invoices WHERE id = ?")->execute([$id]);
            $db->commit();

            jsonSuccess(null, 'Invoice deleted successfully');
            break;

        // ==========================================
        // 7. Update Invoice Status
        // ==========================================
        case 'update_invoice_status':
            $id = (int)($_POST['id'] ?? 0);
            $status = trim($_POST['status'] ?? '');
            $valid = ['paid', 'partial', 'unpaid', 'pending', 'cancelled'];
            if (!$id || !in_array($status, $valid)) {
                jsonError('Invalid status value');
            }

            // Fetch invoice to update partner shares if marked paid
            $stmt = $db->prepare("SELECT * FROM invoices WHERE id = ?");
            $stmt->execute([$id]);
            $inv = $stmt->fetch();
            if (!$inv) jsonError('Invoice not found');

            if ($status === 'paid') {
                $paid = (float)$inv['grand_total'];
                $partner = $inv['partner_assigned'];
                $ibraheemShare = ($partner === 'ibraheem') ? $paid : (($partner === 'fawas') ? 0 : round($paid / 2, 2));
                $fawasShare = ($partner === 'fawas') ? $paid : (($partner === 'ibraheem') ? 0 : round($paid / 2, 2));

                $update = $db->prepare("
                    UPDATE invoices SET 
                        status = 'paid', remaining_balance = 0, paid_amount = grand_total,
                        ibraheem_share = ?, fawas_share = ?
                    WHERE id = ?
                ");
                $update->execute([$ibraheemShare, $fawasShare, $id]);
            } else {
                $stmt = $db->prepare("UPDATE invoices SET status = ? WHERE id = ?");
                $stmt->execute([$status, $id]);
            }

            jsonSuccess(null, 'Invoice status updated successfully');
            break;

        // ==========================================
        // 7b. Quick Click-to-Edit Partner Assignment
        // ==========================================
        case 'update_partner_assignment':
            $id = (int)($_POST['id'] ?? $_POST['invoice_id'] ?? 0);
            $partner = trim($_POST['partner_assigned'] ?? 'both');
            if (!in_array($partner, ['ibraheem', 'fawas', 'both'])) {
                $partner = 'both';
            }

            if (!$id) jsonError('Invoice ID is required');

            $stmt = $db->prepare("SELECT * FROM invoices WHERE id = ?");
            $stmt->execute([$id]);
            $inv = $stmt->fetch();
            if (!$inv) jsonError('Invoice not found');

            $paid = (float)$inv['paid_amount'];
            if ($partner === 'ibraheem') {
                $ibraheemShare = $paid;
                $fawasShare = 0.00;
            } elseif ($partner === 'fawas') {
                $ibraheemShare = 0.00;
                $fawasShare = $paid;
            } else {
                $ibraheemShare = round($paid / 2, 2);
                $fawasShare = round($paid / 2, 2);
            }

            $update = $db->prepare("
                UPDATE invoices SET 
                    partner_assigned = ?,
                    ibraheem_share = ?,
                    fawas_share = ?
                WHERE id = ?
            ");
            $update->execute([$partner, $ibraheemShare, $fawasShare, $id]);

            jsonSuccess([
                'id'               => $id,
                'partner_assigned' => $partner,
                'ibraheem_share'   => $ibraheemShare,
                'fawas_share'      => $fawasShare
            ], 'Partner assignment updated successfully');
            break;

        // ==========================================
        // 8. Quick Record Payment / Balance Update
        // ==========================================
        case 'record_payment':
            $id = (int)($_POST['id'] ?? $_POST['invoice_id'] ?? 0);
            $payment = max(0, (float)($_POST['payment_amount'] ?? 0));
            $paymentMethod = trim($_POST['payment_method'] ?? 'gcash');
            $paymentNote = trim($_POST['payment_note'] ?? '');

            if (!$id) jsonError('Invoice ID is required');

            $stmt = $db->prepare("SELECT * FROM invoices WHERE id = ?");
            $stmt->execute([$id]);
            $inv = $stmt->fetch();
            if (!$inv) jsonError('Invoice not found');

            $newPaid = round((float)$inv['paid_amount'] + $payment, 2);
            $newBalance = max(0, round((float)$inv['grand_total'] - $newPaid, 2));
            $newStatus = ($newBalance <= 0) ? 'paid' : 'partial';

            // Partner share division calculation
            $partner = $inv['partner_assigned'] ?: 'both';
            if ($partner === 'ibraheem') {
                $ibraheemShare = $newPaid;
                $fawasShare = 0.00;
            } elseif ($partner === 'fawas') {
                $ibraheemShare = 0.00;
                $fawasShare = $newPaid;
            } else { // both
                $ibraheemShare = round($newPaid / 2, 2);
                $fawasShare = round($newPaid / 2, 2);
            }

            $noteAppend = $inv['notes'];
            if (!empty($paymentNote)) {
                $noteAppend .= "\n[Payment Recorded: PHP " . number_format($payment, 2) . " via " . strtoupper($paymentMethod) . " (Account: Ferdenand Sabado 09617190588) - " . date('Y-m-d') . ": " . $paymentNote . "]";
            }

            $update = $db->prepare("
                UPDATE invoices SET 
                    paid_amount = ?, remaining_balance = ?, status = ?, payment_method = ?,
                    ibraheem_share = ?, fawas_share = ?, notes = ?
                WHERE id = ?
            ");
            $update->execute([$newPaid, $newBalance, $newStatus, $paymentMethod, $ibraheemShare, $fawasShare, $noteAppend, $id]);

            jsonSuccess([
                'paid_amount'       => $newPaid,
                'remaining_balance' => $newBalance,
                'status'            => $newStatus,
                'ibraheem_share'    => $ibraheemShare,
                'fawas_share'       => $fawasShare
            ], 'Payment recorded successfully');
            break;

        // ==========================================
        // 9. Clients Management
        // ==========================================
        case 'get_clients':
            $search = trim($_GET['search'] ?? '');
            $sql = "
                SELECT c.*, 
                       COUNT(i.id) as invoice_count,
                       COALESCE(SUM(CASE WHEN i.status != 'cancelled' THEN i.grand_total ELSE 0 END), 0) as total_purchases,
                       COALESCE(SUM(CASE WHEN i.status != 'cancelled' THEN i.paid_amount ELSE 0 END), 0) as total_paid,
                       COALESCE(SUM(CASE WHEN i.status != 'cancelled' THEN i.remaining_balance ELSE 0 END), 0) as total_balance
                FROM clients c
                LEFT JOIN invoices i ON c.id = i.client_id
            ";
            $params = [];
            if (!empty($search)) {
                $sql .= " WHERE c.name LIKE ? OR c.contact_person LIKE ? OR c.phone LIKE ? OR c.address LIKE ?";
                $wildcard = "%$search%";
                $params = [$wildcard, $wildcard, $wildcard, $wildcard];
            }
            $sql .= " GROUP BY c.id ORDER BY total_balance DESC, c.id DESC";

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            jsonSuccess($stmt->fetchAll());
            break;

        case 'get_client':
            $id = (int)($_GET['id'] ?? 0);
            if (!$id) jsonError('Client ID is required');

            $stmt = $db->prepare("
                SELECT c.*, 
                       COUNT(i.id) as invoice_count,
                       COALESCE(SUM(CASE WHEN i.status != 'cancelled' THEN i.grand_total ELSE 0 END), 0) as total_purchases,
                       COALESCE(SUM(CASE WHEN i.status != 'cancelled' THEN i.paid_amount ELSE 0 END), 0) as total_paid,
                       COALESCE(SUM(CASE WHEN i.status != 'cancelled' THEN i.remaining_balance ELSE 0 END), 0) as total_balance
                FROM clients c
                LEFT JOIN invoices i ON c.id = i.client_id
                WHERE c.id = ?
                GROUP BY c.id
            ");
            $stmt->execute([$id]);
            $client = $stmt->fetch();
            if (!$client) jsonError('Client not found', 404);

            // Fetch invoices belonging to this client
            $invStmt = $db->prepare("
                SELECT * FROM invoices 
                WHERE client_id = ? 
                ORDER BY issue_date DESC, id DESC
            ");
            $invStmt->execute([$id]);
            $client['invoices'] = $invStmt->fetchAll();

            jsonSuccess($client);
            break;

        case 'save_client':
            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $contactPerson = trim($_POST['contact_person'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $taxNumber = trim($_POST['tax_number'] ?? '');

            if (empty($name)) jsonError('Company or client name is required');

            if ($id > 0) {
                $stmt = $db->prepare("
                    UPDATE clients SET 
                        name = ?, contact_person = ?, phone = ?, email = ?, address = ?, tax_number = ? 
                    WHERE id = ?
                ");
                $stmt->execute([$name, $contactPerson, $phone, $email, $address, $taxNumber, $id]);
            } else {
                $stmt = $db->prepare("
                    INSERT INTO clients (name, contact_person, phone, email, address, tax_number) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$name, $contactPerson, $phone, $email, $address, $taxNumber]);
                $id = (int)$db->lastInsertId();
            }

            jsonSuccess(['id' => $id], 'Client information saved successfully');
            break;

        case 'delete_client':
            $id = (int)($_POST['id'] ?? 0);
            if (!$id) jsonError('Client ID is required');

            $db->prepare("UPDATE invoices SET client_id = NULL WHERE client_id = ?")->execute([$id]);
            $db->prepare("DELETE FROM clients WHERE id = ?")->execute([$id]);

            jsonSuccess(null, 'Client deleted successfully');
            break;

        // ==========================================
        // 10. Products & Services Catalog
        // ==========================================
        case 'get_products':
            $stmt = $db->query("SELECT * FROM products ORDER BY name ASC");
            jsonSuccess($stmt->fetchAll());
            break;

        case 'save_product':
            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $price = (float)($_POST['price'] ?? 0);
            $taxRate = (float)($_POST['tax_rate'] ?? 0);
            $desc = trim($_POST['description'] ?? '');

            if (empty($name)) jsonError('Product/Service name is required');

            if ($id > 0) {
                $stmt = $db->prepare("UPDATE products SET name = ?, price = ?, tax_rate = ?, description = ? WHERE id = ?");
                $stmt->execute([$name, $price, $taxRate, $desc, $id]);
            } else {
                $stmt = $db->prepare("INSERT INTO products (name, price, tax_rate, description) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $price, $taxRate, $desc]);
                $id = (int)$db->lastInsertId();
            }

            jsonSuccess(['id' => $id], 'Product/Service saved successfully');
            break;

        case 'delete_product':
            $id = (int)($_POST['id'] ?? 0);
            if (!$id) jsonError('Product ID is required');
            $db->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
            jsonSuccess(null, 'Product/Service deleted successfully');
            break;

        // ==========================================
        // 11. Settings Management
        // ==========================================
        case 'get_settings':
            $settings = $db->query("SELECT `key`, value FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
            jsonSuccess($settings);
            break;

        case 'save_settings':
            $allowedKeys = [
                'company_name', 'company_cr', 'company_vat', 'company_phone',
                'company_email', 'company_address', 'company_manager', 'company_title',
                'company_cert', 'company_website', 'company_gcash', 'gcash_account_name',
                'partner_1_name', 'partner_2_name', 'company_stc', 'company_logo',
                'currency', 'currency_symbol', 'default_tax_rate', 'invoice_prefix', 'invoice_footer'
            ];

            $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
            if ($driver === 'mysql') {
                $stmt = $db->prepare("INSERT INTO settings (`key`, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)");
            } else {
                $stmt = $db->prepare("INSERT INTO settings (`key`, value) VALUES (?, ?) ON CONFLICT(`key`) DO UPDATE SET value = excluded.value");
            }
            foreach ($allowedKeys as $k) {
                if (isset($_POST[$k])) {
                    $stmt->execute([$k, trim($_POST[$k])]);
                }
            }

            jsonSuccess(null, 'Settings saved successfully');
            break;

        // ==========================================
        // 12. Backup Export & Restore
        // ==========================================
        case 'export_backup':
            $backup = [
                'version'    => '2.7',
                'system'     => 'YARAMAY Computer Maintenance Services',
                'timestamp'  => date('c'),
                'settings'   => $db->query("SELECT `key`, value FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR),
                'clients'    => $db->query("SELECT * FROM clients")->fetchAll(),
                'products'   => $db->query("SELECT * FROM products")->fetchAll(),
                'invoices'   => $db->query("SELECT * FROM invoices")->fetchAll(),
                'items'      => $db->query("SELECT * FROM invoice_items")->fetchAll(),
            ];

            header('Content-Type: application/json; charset=utf-8');
            header('Content-Disposition: attachment; filename="yaramay_backup_' . date('Y-m-d_His') . '.json"');
            echo json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit;

        case 'import_backup':
            // Anti-Malware / Anti-Webshell Upload Inspection
            $scanResult = UploadInspector::scanUploadedFile($_FILES['backup_file'] ?? [], ['json'], 5242880);
            if (!$scanResult['valid']) {
                jsonError($scanResult['error']);
            }

            $data = json_decode($scanResult['content'], true);
            if (!$data || !isset($data['invoices']) || !isset($data['clients'])) {
                jsonError('Invalid backup file structure: missing required database collections.');
            }

            $db->beginTransaction();

            $db->exec("DELETE FROM invoice_items;");
            $db->exec("DELETE FROM invoices;");
            $db->exec("DELETE FROM products;");
            $db->exec("DELETE FROM clients;");

            // Restore clients
            $cStmt = $db->prepare("INSERT INTO clients (id, name, contact_person, phone, email, address, tax_number, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($data['clients'] as $c) {
                $cStmt->execute([
                    $c['id'], 
                    $c['name'], 
                    $c['contact_person'] ?? '', 
                    $c['phone'] ?? '', 
                    $c['email'] ?? '', 
                    $c['address'] ?? '', 
                    $c['tax_number'] ?? '', 
                    $c['created_at'] ?? date('Y-m-d H:i:s')
                ]);
            }

            // Restore products
            $pStmt = $db->prepare("INSERT INTO products (id, name, price, tax_rate, description, created_at) VALUES (?, ?, ?, ?, ?, ?)");
            foreach ($data['products'] as $p) {
                $pStmt->execute([
                    $p['id'], 
                    $p['name'], 
                    $p['price'] ?? 0, 
                    $p['tax_rate'] ?? 0, 
                    $p['description'] ?? '', 
                    $p['created_at'] ?? date('Y-m-d H:i:s')
                ]);
            }

            // Restore invoices
            $iStmt = $db->prepare("
                INSERT INTO invoices (
                    id, invoice_number, client_id, issue_date, due_date, status, payment_method, 
                    payment_term, installment_months, monthly_payment, partner_assigned, ibraheem_share, fawas_share,
                    subtotal, discount, tax_amount, grand_total, paid_amount, remaining_balance, 
                    price_note, amount_due_note, package_includes, notes, submitted_by, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            foreach ($data['invoices'] as $inv) {
                $iStmt->execute([
                    $inv['id'], 
                    $inv['invoice_number'], 
                    $inv['client_id'], 
                    $inv['issue_date'], 
                    $inv['due_date'], 
                    $inv['status'], 
                    $inv['payment_method'],
                    $inv['payment_term'] ?? 'full',
                    $inv['installment_months'] ?? 1,
                    $inv['monthly_payment'] ?? ($inv['grand_total'] ?? 0),
                    $inv['partner_assigned'] ?? 'both',
                    $inv['ibraheem_share'] ?? 0,
                    $inv['fawas_share'] ?? 0,
                    $inv['subtotal'], 
                    $inv['discount'], 
                    $inv['tax_amount'], 
                    $inv['grand_total'],
                    $inv['paid_amount'] ?? 0, 
                    $inv['remaining_balance'] ?? 0,
                    $inv['price_note'] ?? '', 
                    $inv['amount_due_note'] ?? '', 
                    $inv['package_includes'] ?? '',
                    $inv['notes'] ?? '', 
                    $inv['submitted_by'] ?? 'admin', 
                    $inv['created_at'] ?? date('Y-m-d H:i:s')
                ]);
            }

            // Restore line items
            $itemStmt = $db->prepare("INSERT INTO invoice_items (id, invoice_id, item_name, description, quantity, unit_price, discount, tax_rate, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($data['items'] as $it) {
                $itemStmt->execute([
                    $it['id'], 
                    $it['invoice_id'], 
                    $it['item_name'], 
                    $it['description'] ?? '', 
                    $it['quantity'] ?? 1, 
                    $it['unit_price'] ?? 0, 
                    $it['discount'] ?? 0, 
                    $it['tax_rate'] ?? 0, 
                    $it['total'] ?? 0
                ]);
            }

            if (isset($data['settings']) && is_array($data['settings'])) {
                $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
                if ($driver === 'mysql') {
                    $sStmt = $db->prepare("INSERT INTO settings (`key`, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)");
                } else {
                    $sStmt = $db->prepare("INSERT INTO settings (`key`, value) VALUES (?, ?) ON CONFLICT(`key`) DO UPDATE SET value = excluded.value");
                }
                foreach ($data['settings'] as $k => $v) {
                    $sStmt->execute([$k, $v]);
                }
            }

            $db->commit();
            jsonSuccess(null, 'Backup data restored successfully');
            break;

        // ==========================================
        // 13. Security Logs & Threat Intelligence
        // ==========================================
        case 'get_security_logs':
            $logs = $db->query("
                SELECT id, event_type, ip_address, username, details, user_agent, created_at 
                FROM security_logs 
                ORDER BY id DESC 
                LIMIT 50
            ")->fetchAll();

            $cutoff = date('Y-m-d H:i:s', time() - 900);
            $lockoutStmt = $db->prepare("SELECT COUNT(DISTINCT ip_address) FROM login_attempts WHERE attempt_time > ?");
            $lockoutStmt->execute([$cutoff]);
            $activeLockouts = (int)$lockoutStmt->fetchColumn();

            $stats = [
                'total_attacks_blocked' => (int)$db->query("SELECT COUNT(*) FROM security_logs WHERE event_type = 'ATTACK_BLOCKED'")->fetchColumn(),
                'total_failed_logins'   => (int)$db->query("SELECT COUNT(*) FROM security_logs WHERE event_type = 'LOGIN_FAILED'")->fetchColumn(),
                'total_lockouts'        => (int)$db->query("SELECT COUNT(*) FROM security_logs WHERE event_type = 'BRUTE_FORCE_LOCKOUT'")->fetchColumn(),
                'active_lockouts'       => $activeLockouts
            ];

            jsonSuccess([
                'logs'  => $logs,
                'stats' => $stats
            ]);
            break;

        case 'clear_security_logs':
            if (!isSuperAdmin()) {
                jsonError('Only Superadmin can purge security audit logs.');
            }
            $db->exec("DELETE FROM security_logs;");
            $db->exec("DELETE FROM login_attempts;");
            jsonSuccess(null, 'Security logs cleared successfully.');
            break;

        default:
            jsonError('Invalid API action: ' . htmlspecialchars($action), 404);
            break;
    }
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    jsonError('Database/System Error: ' . $e->getMessage(), 500);
}
