<?php
/**
 * YARAMAY Computer Maintenance Services
 * Official Printable Invoice System
 * Matches the official company format with bilingual support (English / Arabic)
 */

require_once __DIR__ . '/config.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    die('Error: Invoice ID is required.');
}

$lang = strtolower($_GET['lang'] ?? 'en');
if ($lang !== 'ar') {
    $lang = 'en';
}
$isEn = ($lang === 'en');
$dir = $isEn ? 'ltr' : 'rtl';

$db = getDatabaseConnection();

// Fetch invoice with client details
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

if (!$invoice) {
    die('Error: Invoice not found.');
}

// Fetch items
$itemStmt = $db->prepare("SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY id ASC");
$itemStmt->execute([$id]);
$items = $itemStmt->fetchAll();

// Fetch settings
$settings = $db->query("SELECT `key`, value FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);

$companyName = $settings['company_name'] ?? 'YARAMAY Computer Maintenance Services';
$companyAddress = $settings['company_address'] ?? '#19 Street Brgy. San Andres Cainta Rizal Cambridge C5 -1G';
$companyPhone = $settings['company_phone'] ?? '+639075251376 (SMS) +966508624264';
$companyWebsite = $settings['company_website'] ?? 'www.yaramay.online';
$companyGcash = $settings['company_gcash'] ?? '09617190588';
$companyManager = $settings['company_manager'] ?? 'Ferdenand (Ibraheem) Sabado';
$companyTitle = $settings['company_title'] ?? 'General Manager';
$companyCert = $settings['company_cert'] ?? 'MCP,MCSE,MCSA and CISCO Certified';
$currency = $settings['currency'] ?? 'PHP';

// Format values
$totalCostFormatted = number_format((float)$invoice['grand_total'], 2);
$paymentFormatted = number_format((float)$invoice['paid_amount'], 2);
$balanceFormatted = number_format((float)$invoice['remaining_balance'], 2);

// Status styling
$statusLabels = [
    'paid' => [
        'text' => $isEn ? 'PAID IN FULL' : 'مدفوعة بالكامل',
        'class' => 'status-badge-paid'
    ],
    'partial' => [
        'text' => $isEn ? 'PARTIAL PAYMENT' : 'دفعة جزئية',
        'class' => 'status-badge-partial'
    ],
    'unpaid' => [
        'text' => $isEn ? 'UNPAID / BALANCE DUE' : 'غير مدفوعة',
        'class' => 'status-badge-unpaid'
    ],
    'pending' => [
        'text' => $isEn ? 'PENDING' : 'قيد الانتظار',
        'class' => 'status-badge-pending'
    ],
    'cancelled' => [
        'text' => $isEn ? 'CANCELLED' : 'ملغاة',
        'class' => 'status-badge-cancelled'
    ]
];
$statusInfo = $statusLabels[$invoice['status']] ?? ['text' => strtoupper($invoice['status']), 'class' => ''];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>" dir="<?= $dir ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($invoice['invoice_number']) ?> - <?= htmlspecialchars($invoice['client_name'] ?? 'Invoice') ?> | YARAMAY</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Cairo:wght@400;600;700;800&family=Alex+Brush&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: <?= $isEn ? "'Inter', sans-serif" : "'Cairo', 'Inter', sans-serif" ?>;
            background-color: #f1f5f9;
            color: #1e293b;
            font-size: 13px;
            line-height: 1.5;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Top Action Bar (Hidden when printing) */
        .no-print-bar {
            background: #ffffff;
            border-bottom: 1px solid #cbd5e1;
            padding: 12px 24px;
            position: sticky;
            top: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0,0,0,0.06);
        }
        .bar-left, .bar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
            border: 1px solid transparent;
        }
        .btn-primary {
            background-color: #1e3a8a;
            color: #ffffff;
        }
        .btn-primary:hover {
            background-color: #172554;
        }
        .btn-secondary {
            background-color: #ffffff;
            color: #334155;
            border-color: #cbd5e1;
        }
        .btn-secondary:hover {
            background-color: #f8fafc;
            border-color: #94a3b8;
        }

        /* Page Container */
        .invoice-page {
            width: 210mm;
            min-height: 297mm;
            margin: 24px auto;
            background: #ffffff;
            padding: 20mm 20mm 15mm 20mm;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

        .invoice-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 440px;
            opacity: 0.042;
            pointer-events: none;
            z-index: 0;
        }

        /* YARAMAY Centered Header */
        .brand-header {
            text-align: center;
            margin-bottom: 24px;
        }
        .logo-emblem {
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 6px;
        }
        .logo-title {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 3px;
            color: #0f172a;
            margin-top: 4px;
        }
        .logo-subtitle {
            font-size: 10px;
            letter-spacing: 0.5px;
            color: #475569;
            font-weight: 500;
        }
        .brand-top-contact {
            font-size: 11px;
            color: #334155;
            margin-top: 6px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 12px;
        }
        .brand-top-contact a {
            color: #0284c7;
            text-decoration: underline;
        }

        /* Invoice Meta & Bill To Grid */
        .invoice-meta-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }
        .bill-to-box {
            max-width: 55%;
        }
        .bill-to-title {
            font-weight: 700;
            font-size: 14px;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .bill-to-line {
            font-size: 13px;
            color: #1e293b;
            margin-bottom: 2px;
        }
        .bill-to-line strong {
            display: inline-block;
            min-width: 80px;
            color: #334155;
        }

        .invoice-details-box {
            text-align: <?= $isEn ? 'right' : 'left' ?>;
        }
        .invoice-number-title {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }
        .meta-line {
            font-size: 13px;
            color: #334155;
            margin-bottom: 2px;
        }

        /* Status Pill */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            border-radius: 4px;
            margin-top: 6px;
            text-transform: uppercase;
        }
        .status-badge-paid {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
        }
        .status-badge-partial {
            background-color: #fef3c7;
            color: #b45309;
            border: 1px solid #fcd34d;
        }
        .status-badge-unpaid {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }
        .status-badge-pending {
            background-color: #e0f2fe;
            color: #0369a1;
            border: 1px solid #7dd3fc;
        }

        /* Items Table */
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            border: 1px solid #0f172a;
        }
        .invoice-table th {
            border: 1px solid #0f172a;
            padding: 8px 10px;
            font-size: 13px;
            font-weight: 700;
            background-color: #f8fafc;
            color: #0f172a;
            text-align: center;
        }
        .invoice-table th.col-desc {
            text-align: <?= $isEn ? 'left' : 'right' ?>;
            width: 65%;
        }
        .invoice-table th.col-price {
            width: 18%;
        }
        .invoice-table th.col-amount {
            width: 17%;
        }

        .invoice-table td {
            border: 1px solid #0f172a;
            padding: 10px;
            vertical-align: top;
            font-size: 12px;
        }
        .invoice-table td.col-desc {
            text-align: <?= $isEn ? 'left' : 'right' ?>;
        }
        .invoice-table td.col-price,
        .invoice-table td.col-amount {
            text-align: center;
            font-weight: 600;
            white-space: pre-line;
        }

        .package-title {
            font-weight: 700;
            font-size: 13px;
            color: #0f172a;
            margin-bottom: 6px;
        }
        .package-desc {
            font-size: 11.5px;
            line-height: 1.6;
            color: #1e293b;
            white-space: pre-line;
        }

        /* Summary Calculations Table (Bottom Right) */
        .summary-wrapper {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }
        .summary-table {
            width: 45%;
            border-collapse: collapse;
            border: 1px solid #0f172a;
        }
        .summary-table td {
            border: 1px solid #0f172a;
            padding: 6px 12px;
            font-size: 13px;
        }
        .summary-label {
            font-weight: 700;
            color: #0f172a;
            width: 50%;
        }
        .summary-val {
            text-align: <?= $isEn ? 'right' : 'left' ?>;
            font-weight: 700;
            color: #0f172a;
        }
        .balance-highlight {
            background-color: #f8fafc;
        }
        .balance-highlight .summary-val {
            color: <?= ((float)$invoice['remaining_balance'] > 0) ? '#b91c1c' : '#15803d' ?>;
            font-size: 14px;
        }

        /* Bottom Suggestions Box */
        .suggestions-box {
            border: 1px solid #0f172a;
            padding: 10px 14px;
            margin-bottom: 24px;
            background: #ffffff;
            max-width: 72%;
        }
        .suggestions-text {
            font-size: 12px;
            color: #1e293b;
            line-height: 1.5;
        }
        .gcash-highlight {
            font-weight: 700;
            color: #0f172a;
        }

        /* Thank you & Signature Block */
        .signoff-section {
            margin-bottom: 24px;
        }
        .thank-you-text {
            font-size: 13px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .yours-truly {
            font-size: 13px;
            color: #334155;
            margin-bottom: 4px;
        }
        .signature-container {
            margin: 6px 0;
            min-height: 48px;
            display: flex;
            align-items: center;
        }
        .signature-script {
            font-family: 'Alex Brush', cursive, sans-serif;
            font-size: 34px;
            color: #1e3a8a;
            transform: rotate(-3deg);
            display: inline-block;
            user-select: none;
        }
        .signature-line {
            width: 220px;
            border-bottom: 1px solid #0f172a;
            margin-bottom: 6px;
        }
        .manager-name {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }
        .manager-title {
            font-size: 11.5px;
            color: #475569;
        }
        .manager-cert {
            font-size: 10.5px;
            color: #64748b;
            font-weight: 600;
        }

        /* Bottom Fixed Footer */
        .invoice-page-footer {
            border-top: 2px solid #0f172a;
            padding-top: 8px;
            text-align: center;
            font-size: 11px;
            color: #334155;
            margin-top: auto;
        }
        .invoice-page-footer a {
            color: #0284c7;
            text-decoration: underline;
        }

        /* Responsive Mobile & Tablet Preview */
        @media screen and (max-width: 820px) {
            .no-print-bar {
                padding: 10px 14px;
                flex-wrap: wrap;
                gap: 8px;
            }
            .bar-left, .bar-right {
                flex-wrap: wrap;
                gap: 6px;
            }
            .btn-action {
                padding: 7px 12px;
                font-size: 12px;
            }
            .invoice-page {
                width: calc(100% - 20px);
                min-height: auto;
                margin: 12px auto;
                padding: 18px 14px;
            }
            .invoice-watermark {
                width: 240px;
            }
            .invoice-meta-row {
                flex-direction: column;
                gap: 14px;
            }
            .bill-to-box,
            .invoice-details-box {
                max-width: 100%;
                width: 100%;
                text-align: <?= $isEn ? 'left' : 'right' ?>;
            }
            .summary-table {
                width: 100%;
            }
            .suggestions-box {
                max-width: 100%;
            }
            .invoice-table th,
            .invoice-table td {
                padding: 6px;
                font-size: 11px;
            }
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .no-print-bar {
                display: none !important;
            }
            .invoice-page {
                box-shadow: none !important;
                margin: 0 !important;
                width: 100% !important;
                min-height: 100vh !important;
                padding: 12mm 15mm 12mm 15mm !important;
            }
        }
    </style>
</head>
<body>

<!-- Interactive Toolbar (Hidden in Print) -->
<div class="no-print-bar">
    <div class="bar-left">
        <a href="index.php" class="btn-action btn-secondary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <?= $isEn ? 'Back to Dashboard' : 'العودة للوحة التحكم' ?>
        </a>
        <button onclick="window.print()" class="btn-action btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <?= $isEn ? 'Print Invoice' : 'طباعة الفاتورة' ?>
        </button>
    </div>
    <div class="bar-right">
        <?php if ($isEn): ?>
            <a href="print.php?id=<?= $id ?>&lang=ar" class="btn-action btn-secondary">
                🌐 Switch to Arabic (العربية)
            </a>
        <?php else: ?>
            <a href="print.php?id=<?= $id ?>&lang=en" class="btn-action btn-secondary">
                🌐 Switch to English
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Main Printable A4 Invoice Container -->
<div class="invoice-page">

    <!-- Subtle Authentic Background Watermark -->
    <img src="assets/images/yaramay-logo-transparent.png" class="invoice-watermark" alt="" aria-hidden="true">

    <div>
        <!-- Top Centered Header with Authentic YARAMAY Logo -->
        <header class="brand-header">
            <div class="logo-emblem">
                <img src="assets/images/yaramay-logo.png" alt="YARAMAY Computer Maintenance Services" style="height:78px; width:auto; display:block; margin:0 auto 4px auto;">
            </div>
            <div class="brand-top-contact">
                #19 Street Brgy. San Andres Cainta Rizal Cambridge C5 -1G: Contact # +639075251376 (SMS) +966508624264<br>
                <a href="https://<?= htmlspecialchars($companyWebsite) ?>" target="_blank"><?= htmlspecialchars($companyWebsite) ?></a>
            </div>
        </header>

        <!-- Invoice Meta Details & Bill To -->
        <div class="invoice-meta-row">
            <div class="bill-to-box">
                <div class="bill-to-title"><?= $isEn ? 'Bill to:' : 'فاتورة إلى:' ?></div>
                <div class="bill-to-line">
                    <strong><?= $isEn ? 'Name –' : 'الاسم –' ?></strong>
                    <span><?= htmlspecialchars($invoice['contact_person'] ?? $invoice['client_name']) ?></span>
                </div>
                <div class="bill-to-line">
                    <strong><?= $isEn ? 'Company –' : 'الشركة –' ?></strong>
                    <span><?= htmlspecialchars($invoice['client_name']) ?></span>
                </div>
                <div class="bill-to-line">
                    <strong><?= $isEn ? 'Address –' : 'العنوان –' ?></strong>
                    <span><?= htmlspecialchars($invoice['client_address'] ?? 'Manila, Philippines') ?></span>
                </div>
                <?php if (!empty($invoice['client_phone'])): ?>
                <div class="bill-to-line">
                    <strong><?= $isEn ? 'Phone –' : 'الهاتف –' ?></strong>
                    <span><?= htmlspecialchars($invoice['client_phone']) ?></span>
                </div>
                <?php endif; ?>
            </div>

            <div class="invoice-details-box">
                <div class="meta-line">
                    <strong><?= $isEn ? 'Date:' : 'التاريخ:' ?></strong>
                    <?= date('F d, Y', strtotime($invoice['issue_date'])) ?>
                </div>
                <div class="invoice-number-title">
                    <?= $isEn ? 'Invoice #:' : 'رقم الفاتورة:' ?> <?= htmlspecialchars($invoice['invoice_number']) ?>
                </div>
                <div>
                    <span class="status-badge <?= $statusInfo['class'] ?>"><?= $statusInfo['text'] ?></span>
                </div>
            </div>
        </div>

        <!-- Description Table -->
        <table class="invoice-table">
            <thead>
                <tr>
                    <th class="col-desc"><?= $isEn ? 'Description' : 'الوصف والخدمات' ?></th>
                    <th class="col-price"><?= $isEn ? 'Price' : 'السعر' ?></th>
                    <th class="col-amount"><?= $isEn ? 'Amount Due' : 'المبلغ المستحق' ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $it): ?>
                <tr>
                    <td class="col-desc">
                        <div class="package-title"><?= htmlspecialchars($it['item_name']) ?></div>
                        <div class="package-desc"><?= nl2br(htmlspecialchars($it['description'])) ?></div>
                    </td>
                    <td class="col-price">
                        <?php if (!empty($invoice['price_note'])): ?>
                            <?= nl2br(htmlspecialchars($invoice['price_note'])) ?>
                        <?php else: ?>
                            PHP <?= number_format($it['unit_price'], 2) ?>
                        <?php endif; ?>
                    </td>
                    <td class="col-amount">
                        <?php if (!empty($invoice['amount_due_note'])): ?>
                            <?= nl2br(htmlspecialchars($invoice['amount_due_note'])) ?>
                        <?php else: ?>
                            <?= number_format($it['total'], 2) ?> PHP
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Calculations Summary Box (Total Cost, Payment, Balance) -->
        <div class="summary-wrapper">
            <table class="summary-table">
                <tr>
                    <td class="summary-label"><?= $isEn ? 'Total Cost' : 'التكلفة الإجمالية' ?></td>
                    <td class="summary-val">php <?= $totalCostFormatted ?></td>
                </tr>
                <?php if (!empty($invoice['installment_months']) && (int)$invoice['installment_months'] > 1): ?>
                <tr>
                    <td class="summary-label"><?= $isEn ? 'Payment Plan' : 'خطة التقسيط' ?></td>
                    <td class="summary-val" style="font-size: 11px; color: #1e3a8a;">
                        <?= (int)$invoice['installment_months'] ?> <?= $isEn ? 'Months' : 'أشهر' ?> 
                        (₱<?= number_format((float)$invoice['grand_total'], 2) ?> ÷ <?= (int)$invoice['installment_months'] ?> = ₱<?= number_format((float)$invoice['monthly_payment'], 2) ?>/<?= $isEn ? 'mo' : 'شهر' ?>)
                    </td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td class="summary-label"><?= $isEn ? 'Payment' : 'المبلغ المسدد' ?></td>
                    <td class="summary-val">Php <?= $paymentFormatted ?></td>
                </tr>
                <tr class="balance-highlight">
                    <td class="summary-label"><?= $isEn ? 'Balance' : 'المتبقي (الرصيد)' ?></td>
                    <td class="summary-val">Php <?= $balanceFormatted ?></td>
                </tr>
            </table>
        </div>

        <!-- Suggestions & Payment Transfer Note Box -->
        <div class="suggestions-box">
            <div class="suggestions-text">
                <?= $isEn ? 'We are open for your suggestions on improving our services with you.' : 'نرحب دائماً باقتراحاتكم لتطوير خدماتنا معكم.' ?><br>
                <?= $isEn ? 'Payment can be transferred through Cash or GCash:' : 'يمكن سداد المبلغ نقداً أو عبر GCash:' ?><br>
                <span class="gcash-highlight"><?= $isEn ? 'GCash Account Name:' : 'اسم حساب GCash:' ?> Ferdenand Sabado</span><br>
                <span class="gcash-highlight"><?= $isEn ? 'GCash Mobile #:' : 'رقم GCash:' ?> <?= htmlspecialchars($companyGcash) ?></span>
                <?php if (!empty($invoice['partner_assigned'])): ?>
                    <div style="margin-top: 4px; font-size: 11px; color: #475569;">
                        <?= $isEn ? 'Account Officer / Share:' : 'المسؤول / الشريك:' ?>
                        <strong><?= $invoice['partner_assigned'] === 'both' ? 'Both (Mr. Ibraheem & Mr. Fawas - 50/50 Split)' : ($invoice['partner_assigned'] === 'ibraheem' ? 'Mr. Ibraheem (100%)' : 'Mr. Fawas (100%)') ?></strong>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Thank you & Sign-off Block -->
        <div class="signoff-section">
            <div class="thank-you-text"><?= $isEn ? 'Thanking you for doing Business with us!' : 'شكراً لتعاملكم واختياركم لنا!' ?></div>
            <div class="yours-truly"><?= $isEn ? 'Yours Truly,' : 'مع خالص التحية،' ?></div>
            
            <div class="signature-container">
                <span class="signature-script">Ferdenand Sabado</span>
            </div>
            <div class="signature-line"></div>
            
            <div class="manager-name"><?= htmlspecialchars($companyManager) ?></div>
            <div class="manager-title"><?= htmlspecialchars($companyTitle) ?></div>
            <?php if (!empty($companyCert)): ?>
                <div class="manager-cert"><?= htmlspecialchars($companyCert) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Page Footer -->
    <footer class="invoice-page-footer">
        #19 Street Brgy. San Andres Cainta Rizal Cambridge C5 -1G: Contact # +639075251376 (SMS) +966508624264<br>
        <a href="https://<?= htmlspecialchars($companyWebsite) ?>" target="_blank"><?= htmlspecialchars($companyWebsite) ?></a>
    </footer>

</div>

</body>
</html>
