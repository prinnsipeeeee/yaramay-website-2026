<?php
/**
 * YARAMAY Computer Maintenance Services
 * Invoice Record System - Main Application
 * Corporate Identity: Navy Blue & Crimson Red
 * Bilingual Support: English (Default) & Arabic
 */
require_once __DIR__ . '/config.php';
requireAuth();
$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YARAMAY Computer Maintenance Services | Invoice Record System</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom Theme Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Authentic YARAMAY Favicon -->
    <link rel="icon" type="image/png" href="assets/images/favicon.png">
</head>
<body>

<div class="app-container">

    <!-- Mobile Sidebar Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- App Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="brand-wrapper" style="display:flex; align-items:center; gap:12px;">
                <!-- Authentic YARAMAY Corporate Logo -->
                <div class="brand-logo-card" style="background:#ffffff; padding:4px 6px; border-radius:8px; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 8px rgba(0,0,0,0.18);">
                    <img src="assets/images/yaramay-logo.png" alt="YARAMAY Logo" style="height:36px; width:auto; object-fit:contain; display:block;">
                </div>
                <div>
                    <div class="brand-title" data-i18n="brand_title" style="letter-spacing:1px; font-weight:800;">YARAMAY</div>
                    <div class="brand-subtitle" id="sidebarBrandName" data-i18n="brand_subtitle" style="font-size:10.5px; opacity:0.85; line-height:1.2;">Computer Maintenance</div>
                </div>
            </div>
            <button class="sidebar-close-btn" id="closeSidebarBtn" title="Close Menu">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <nav class="sidebar-nav">
            <a href="#" class="nav-link active" data-view="dashboard">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <span data-i18n="nav_dashboard">Admin Dashboard</span>
            </a>

            <a href="#" class="nav-link" data-view="invoices">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span data-i18n="nav_invoices">Invoices Record</span>
            </a>

            <a href="#" class="nav-link" data-view="clients">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span data-i18n="nav_clients">Clients Directory</span>
            </a>

            <a href="#" class="nav-link" data-view="users">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span data-i18n="nav_users">Admin Users</span>
            </a>

            <a href="#" class="nav-link" data-view="security">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span data-i18n="nav_security">Security Shield & Logs</span>
            </a>

            <a href="portal.php" target="_blank" class="nav-link">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                <span data-i18n="nav_client_portal">Client Invoice Portal ↗</span>
            </a>

            <a href="#" class="nav-link" data-view="products">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <span data-i18n="nav_products">Products & Services</span>
            </a>

            <a href="#" class="nav-link" data-view="settings">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span data-i18n="nav_settings">Company Settings</span>
            </a>

            <a href="#" class="nav-link" data-view="backup">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span data-i18n="nav_backup">Backup & Restore</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <span data-i18n="version_text">v2.7 Corporate Edition</span>
            <span style="font-size:10px; background:rgba(255,255,255,0.15); padding:2px 6px; border-radius:4px;">EN / AR</span>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        
        <!-- Top Sticky Navbar -->
        <header class="topbar">
            <div class="topbar-right">
                <button class="mobile-toggle-btn" id="mobileToggleBtn" title="Open Menu">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="page-headline">
                    <h2 id="pageTitle" data-i18n="title_dashboard">Admin Dashboard & Partner Earnings</h2>
                    <p data-i18n="page_subtitle">Billing, Client Dossier & Invoice Management System</p>
                </div>
            </div>

            <div class="topbar-actions">
                <!-- Language Toggle Button (English <-> Arabic) -->
                <button class="lang-btn" id="langToggleBtn" onclick="App.toggleLanguage()" title="Switch Language">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                    <span id="langToggleText">العربية</span>
                </button>

                <!-- External Client Portal Link -->
                <a href="portal.php" target="_blank" class="btn btn-outline btn-sm" title="Open Dedicated Client Portal in new tab" style="text-decoration:none;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    <span data-i18n="nav_client_portal">Client Portal ↗</span>
                </a>

                <!-- Export CSV/Excel -->
                <button class="btn btn-outline btn-sm" onclick="App.exportInvoicesToCSV()" title="Export CSV / Excel">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span data-i18n="btn_export_excel">Export Excel</span>
                </button>

                <!-- Create Invoice Button -->
                <button class="btn btn-primary" onclick="App.openNewInvoiceModal()">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span data-i18n="btn_new_invoice">New Invoice</span>
                </button>

                <!-- User Profile & Logout -->
                <div class="user-profile-badge" style="display:inline-flex; align-items:center; gap:8px; background:var(--bg-muted); padding:5px 12px; border-radius:24px; border:1px solid var(--border);">
                    <div style="width:28px; height:28px; border-radius:50%; background:var(--primary); color:#ffffff; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:12px;">
                        <?= strtoupper(substr($currentUser['username'] ?? 'I', 0, 1)) ?>
                    </div>
                    <div style="font-size:12px; line-height:1.2; text-align:left;">
                        <strong style="color:var(--text-heading); display:block;" id="topbarUsername"><?= htmlspecialchars($currentUser['username'] ?? 'ibraheem01') ?></strong>
                        <span style="font-size:10px; color:var(--accent); font-weight:700; text-transform:uppercase;" id="topbarUserRole"><?= htmlspecialchars($currentUser['role'] ?? 'superadmin') ?></span>
                    </div>
                    <button class="btn btn-outline" onclick="App.openChangeMyPasswordModal()" title="Change My Password" style="padding:4px 8px; font-size:11.5px; border-radius:6px; margin-left:4px;">
                        🔑 Password
                    </button>
                    <a href="logout.php" class="btn btn-outline" style="padding:4px 8px; font-size:11.5px; color:#dc2626; border-color:#fca5a5; border-radius:6px; text-decoration:none;" title="Logout">
                        Logout
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="content-area">

            <!-- ==============================================
                 VIEW 1: Admin Dashboard
                 ============================================== -->
            <section class="view-section active-view" id="view-dashboard">
                
                <!-- Main KPI Statistics Grid -->
                <div class="kpi-grid">
                    <div class="kpi-card total">
                        <div class="kpi-content">
                            <h3 data-i18n="kpi_total_sales">Total Invoiced (Price)</h3>
                            <div class="kpi-value" id="kpiTotalSales">0.00 PHP</div>
                            <span class="kpi-sub" data-i18n="kpi_total_sub">Grand total of issued invoices</span>
                        </div>
                        <div class="kpi-icon">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>

                    <div class="kpi-card paid">
                        <div class="kpi-content">
                            <h3 data-i18n="kpi_paid_total">Total Payments Received</h3>
                            <div class="kpi-value" id="kpiPaidTotal">0.00 PHP</div>
                            <span class="kpi-sub" data-i18n="kpi_paid_sub">Successfully collected funds</span>
                        </div>
                        <div class="kpi-icon">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>

                    <!-- Prominent Remaining Balance KPI Card -->
                    <div class="kpi-card balance">
                        <div class="kpi-content">
                            <h3 data-i18n="kpi_unpaid_total">Remaining Balance Due</h3>
                            <div class="kpi-value" id="kpiUnpaidTotal" style="color:#b91c1c;">0.00 PHP</div>
                            <span class="kpi-sub" data-i18n="kpi_unpaid_sub">Total outstanding receivables</span>
                        </div>
                        <div class="kpi-icon">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>

                    <div class="kpi-card" style="border-right:4px solid #3b82f6;">
                        <div class="kpi-content">
                            <h3 data-i18n="kpi_client_count">Total Clients</h3>
                            <div class="kpi-value" id="kpiClientCount">0</div>
                            <span class="kpi-sub" data-i18n="kpi_client_sub">Registered corporate accounts</span>
                        </div>
                        <div class="kpi-icon" style="background:#eff6ff; color:#3b82f6;">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                    </div>
                </div>

                <!-- PARTNER REVENUE DIVISION SECTION (Mr. Ibraheem & Mr. Fawas) -->
                <div style="margin-bottom:24px;">
                    <div style="margin-bottom:10px;">
                        <h4 style="font-size:16px; font-weight:800; color:var(--text-heading);" data-i18n="partner_split_title">Partner Revenue Division (Mr. Ibraheem & Mr. Fawas)</h4>
                        <p style="font-size:12.5px; color:var(--text-muted);" data-i18n="partner_split_desc">Individual assignments credit 100% of collected client payments to that partner. When set to Both, payments are divided equally (50% / 50%).</p>
                    </div>

                    <div class="partner-summary-box">
                        <div class="partner-card ibraheem">
                            <div>
                                <div style="font-size:12px; font-weight:700; color:#1e40af; text-transform:uppercase;">Mr. Ibraheem's Account</div>
                                <div style="font-size:24px; font-weight:800; color:#1e3a8a; margin:4px 0;" id="kpiIbraheemTotal">0.00 PHP</div>
                                <div style="font-size:11.5px; color:#64748b;">Direct payments + 50% shared earnings</div>
                            </div>
                            <div style="width:48px; height:48px; background:#eff6ff; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#1e40af; font-size:20px;">
                                👤
                            </div>
                        </div>

                        <div class="partner-card fawas">
                            <div>
                                <div style="font-size:12px; font-weight:700; color:#7e22ce; text-transform:uppercase;">Mr. Fawas's Account</div>
                                <div style="font-size:24px; font-weight:800; color:#7e22ce; margin:4px 0;" id="kpiFawasTotal">0.00 PHP</div>
                                <div style="font-size:11.5px; color:#64748b;">Direct payments + 50% shared earnings</div>
                            </div>
                            <div style="width:48px; height:48px; background:#f3e8ff; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#7e22ce; font-size:20px;">
                                👤
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ADMIN VIEW: Client Information & Receivables Overview -->
                <div class="card" style="margin-bottom:24px;">
                    <div class="card-header">
                        <div class="card-title">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span data-i18n="client_info_summary_title">Client Information & Receivables Overview</span>
                        </div>
                        <button class="btn btn-outline btn-sm" onclick="App.switchView('clients')">
                            <span data-i18n="nav_clients">Full Directory</span> &rarr;
                        </button>
                    </div>
                    <div class="card-body" style="padding:0;">
                        <div class="table-container" style="border:none; border-radius:0;">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th data-i18n="th_client">Client / Company</th>
                                        <th data-i18n="th_contact_person">Contact Person</th>
                                        <th data-i18n="th_invoices_count">Invoices</th>
                                        <th data-i18n="th_price">Total Cost / Price</th>
                                        <th data-i18n="th_paid">Amount Paid</th>
                                        <th data-i18n="th_balance">Remaining Balance</th>
                                        <th style="width: 140px;" data-i18n="th_actions">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="adminClientsSummaryTableBody">
                                    <!-- Dynamic rows loaded via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Recent Invoices Table -->
                <div class="card" style="margin-bottom:24px;">
                    <div class="card-header">
                        <div class="card-title">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span data-i18n="recent_invoices_title">Recent Invoices & Transactions</span>
                        </div>
                        <button class="btn btn-outline btn-sm" onclick="App.switchView('invoices')" data-i18n="btn_view_all_invoices">
                            View All Invoices &rarr;
                        </button>
                    </div>
                    <div class="card-body" style="padding:0;">
                        <div class="table-container" style="border:none; border-radius:0;">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th data-i18n="th_invoice_num">Invoice #</th>
                                        <th data-i18n="th_client">Client / Company</th>
                                        <th data-i18n="th_price">Price & Plan</th>
                                        <th data-i18n="th_paid">Paid</th>
                                        <th data-i18n="th_balance">Remaining Balance</th>
                                        <th data-i18n="th_partner">Assigned Partner</th>
                                        <th data-i18n="th_status">Status</th>
                                        <th style="width: 130px;" data-i18n="th_actions">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="recentInvoicesTableBody">
                                    <!-- Dynamic rows loaded via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </section>

            <!-- ==============================================
                 VIEW 2: Invoices List & Management
                 ============================================== -->
            <section class="view-section" id="view-invoices">
                
                <!-- Filters & Search Bar -->
                <div class="filter-bar">
                    <div class="search-input-group">
                        <svg class="search-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" id="invoiceSearchInput" class="search-input" data-i18n-placeholder="search_invoice_placeholder" placeholder="Search by invoice #, client name, contact, phone...">
                    </div>

                    <select id="statusFilterSelect" class="filter-select">
                        <option value="all" data-i18n="status_all">All Statuses</option>
                        <option value="paid" data-i18n="status_paid">Paid in Full</option>
                        <option value="partial" data-i18n="status_partial">Partial Payment</option>
                        <option value="unpaid" data-i18n="status_unpaid">Unpaid (Due)</option>
                        <option value="pending" data-i18n="status_pending">Pending</option>
                        <option value="cancelled" data-i18n="status_cancelled">Cancelled</option>
                    </select>

                    <select id="partnerFilterSelect" class="filter-select">
                        <option value="all" data-i18n="partner_all">All Partners</option>
                        <option value="both" data-i18n="partner_both">Both (50/50 Split)</option>
                        <option value="ibraheem" data-i18n="partner_ibraheem">Mr. Ibraheem</option>
                        <option value="fawas" data-i18n="partner_fawas">Mr. Fawas</option>
                    </select>

                    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                        <input type="date" id="dateFromFilter" class="filter-select">
                        <span style="color:var(--text-muted); font-size:12px;" data-i18n="date_to">To</span>
                        <input type="date" id="dateToFilter" class="filter-select">
                    </div>

                    <button class="btn btn-outline btn-sm" onclick="document.getElementById('invoiceSearchInput').value=''; document.getElementById('statusFilterSelect').value='all'; document.getElementById('partnerFilterSelect').value='all'; document.getElementById('dateFromFilter').value=''; document.getElementById('dateToFilter').value=''; App.loadInvoices();" data-i18n="btn_reset">
                        Reset
                    </button>
                </div>

                <!-- Invoices Table -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <span data-i18n="title_invoices">Invoices Record & Payment Management</span>
                        </div>
                        <button class="btn btn-primary btn-sm" onclick="App.openNewInvoiceModal()">
                            <span>+ </span><span data-i18n="btn_new_invoice">Create Invoice</span>
                        </button>
                    </div>
                    <div class="card-body" style="padding:0;">
                        <div class="table-container" style="border:none;">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th data-i18n="th_invoice_num">Invoice #</th>
                                        <th data-i18n="th_client">Client / Company</th>
                                        <th data-i18n="th_price">Price & Plan</th>
                                        <th data-i18n="th_paid">Amount Paid</th>
                                        <th data-i18n="th_balance">Remaining Balance</th>
                                        <th data-i18n="th_partner">Partner Assigned</th>
                                        <th data-i18n="th_status">Status</th>
                                        <th style="width: 220px;" data-i18n="th_actions">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="invoicesTableBody">
                                    <!-- Dynamic rows loaded via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </section>

            <!-- ==============================================
                 VIEW 3: Clients Directory & Statements
                 ============================================== -->
            <section class="view-section" id="view-clients">
                
                <div class="filter-bar">
                    <div class="search-input-group">
                        <svg class="search-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" id="clientSearchInput" class="search-input" data-i18n-placeholder="search_client_placeholder" placeholder="Search clients by company name, contact, phone, address...">
                    </div>
                    <button class="btn btn-primary btn-sm" onclick="App.openNewClientModal()">
                        <span>+ </span><span data-i18n="btn_add_client">Add Client</span>
                    </button>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span data-i18n="title_clients">Clients Directory & Financial Statements</span>
                        </div>
                    </div>
                    <div class="card-body" style="padding:0;">
                        <div class="table-container" style="border:none;">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th data-i18n="th_client">Company / Client Name</th>
                                        <th data-i18n="th_contact_person">Contact Person</th>
                                        <th data-i18n="th_invoices_count">Invoices</th>
                                        <th data-i18n="th_price">Total Billed</th>
                                        <th data-i18n="th_paid">Total Paid</th>
                                        <th data-i18n="th_balance">Remaining Balance</th>
                                        <th style="width: 210px;" data-i18n="th_actions">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="clientsTableBody">
                                    <!-- Dynamic rows loaded via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </section>

            <!-- ==============================================
                 VIEW 4: Client Invoice Portal (Installment Formula & Fill-up)
                 ============================================== -->
            <section class="view-section" id="view-client_portal">
                
                <div class="portal-card" id="portalFormSection">
                    <div style="border-bottom:1px solid var(--border); padding-bottom:16px; margin-bottom:24px;">
                        <h3 style="font-size:22px; font-weight:800; color:var(--text-heading);" data-i18n="client_portal_headline">Client Fill-up Form for Invoice Generation</h3>
                        <p style="color:var(--text-muted); font-size:13.5px; margin-top:4px;" data-i18n="client_portal_sub">Fill out this form to request or generate an invoice with full payment, 3 months, or 6 months installments.</p>
                    </div>

                    <!-- Package Presets Quick Picker -->
                    <div style="margin-bottom:24px;">
                        <label style="font-weight:700; font-size:14px; margin-bottom:10px; display:block;">Select Service Package or Plan:</label>
                        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:12px;">
                            <div class="package-choice-card selected" id="pkgCard_monthly_rescue" onclick="App.selectPortalPackage('monthly_rescue')">
                                <div style="font-weight:800; font-size:14px; color:var(--primary);">E-Commerce Maintenance & Rescue System</div>
                                <div style="font-size:16px; font-weight:800; color:var(--primary); margin:6px 0;">PHP 7,500 <span style="font-size:11px; font-weight:400; color:#64748b;">/ month</span></div>
                                <div style="font-size:11.5px; color:#475569;">10-Point Complete Package with Hosting, SSL & Rescue System</div>
                            </div>

                            <div class="package-choice-card" id="pkgCard_annual_rescue" onclick="App.selectPortalPackage('annual_rescue')">
                                <div style="font-weight:800; font-size:14px; color:var(--primary);">E-Commerce Maintenance (Annual Plan)</div>
                                <div style="font-size:16px; font-weight:800; color:var(--primary); margin:6px 0;">PHP 90,000 <span style="font-size:11px; font-weight:400; color:#64748b;">/ year</span></div>
                                <div style="font-size:11.5px; color:#475569;">Full 12-month enterprise contract with installment support</div>
                            </div>

                            <div class="package-choice-card" id="pkgCard_web_online" onclick="App.selectPortalPackage('web_online')">
                                <div style="font-weight:800; font-size:14px; color:var(--primary);">Web & System (Online) Maintenance</div>
                                <div style="font-size:16px; font-weight:800; color:var(--primary); margin:6px 0;">PHP 5,000 <span style="font-size:11px; font-weight:400; color:#64748b;">/ month</span></div>
                                <div style="font-size:11.5px; color:#475569;">Online system maintenance, bug fixes and routine server upkeep</div>
                            </div>

                            <div class="package-choice-card" id="pkgCard_hardware_support" onclick="App.selectPortalPackage('hardware_support')">
                                <div style="font-weight:800; font-size:14px; color:var(--primary);">Hardware & Server Technical Support</div>
                                <div style="font-size:16px; font-weight:800; color:var(--primary); margin:6px 0;">PHP 5,500 <span style="font-size:11px; font-weight:400; color:#64748b;">/ month</span></div>
                                <div style="font-size:11.5px; color:#475569;">Hardware support, email server maintenance and remote rescue</div>
                            </div>
                        </div>
                    </div>

                    <form id="clientPortalForm">
                        <div class="form-grid">
                            <div class="form-group">
                                <label data-i18n="lbl_client_name">Company / Organization Name *</label>
                                <input type="text" name="client_name" id="portalClientName" class="form-control" placeholder="e.g. The Jobconnection Manpower" required>
                            </div>

                            <div class="form-group">
                                <label data-i18n="lbl_contact_person">Contact Person Name *</label>
                                <input type="text" name="contact_person" id="portalContactPerson" class="form-control" placeholder="e.g. Mr. Allan Rom / Ms. Pilar Corporal" required>
                            </div>

                            <div class="form-group">
                                <label data-i18n="lbl_client_phone">Phone / Mobile (SMS/WhatsApp)</label>
                                <input type="text" name="phone" id="portalPhone" class="form-control" placeholder="+63 907 525 1376">
                            </div>

                            <div class="form-group">
                                <label data-i18n="lbl_client_email">Email Address</label>
                                <input type="email" name="email" id="portalEmail" class="form-control" placeholder="billing@company.com">
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom:16px;">
                            <label data-i18n="lbl_client_address">Complete Billing Address / Location *</label>
                            <input type="text" name="address" id="portalAddress" class="form-control" placeholder="e.g. Ped Gil, Manila / Makati City" required>
                        </div>

                        <!-- Price & Service Name -->
                        <div class="form-grid">
                            <div class="form-group" style="grid-column: span 2;">
                                <label>Service Package Name *</label>
                                <input type="text" name="service_name" id="portalServiceName" class="form-control" value="Package of E-Commerce Maintenance Services and Web maintenance including Rescue System" required>
                            </div>

                            <div class="form-group">
                                <label>Overall Total Price (PHP) *</label>
                                <input type="number" step="0.01" min="0" name="price" id="portalPrice" class="form-control" value="7500.00" required>
                            </div>
                        </div>

                        <!-- PAYMENT TERM & INSTALLMENT FORMULA SELECTOR -->
                        <div style="background:#eff6ff; border:1px solid #bfdbfe; padding:18px; border-radius:var(--radius-md); margin-bottom:18px;">
                            <label style="font-weight:700; color:var(--primary); font-size:14px; margin-bottom:8px; display:block;">
                                📅 Payment Plan / Installment Option:
                            </label>
                            <div style="display:flex; flex-wrap:wrap; gap:16px; margin-bottom:12px;">
                                <label style="display:inline-flex; align-items:center; gap:6px; cursor:pointer; font-weight:600;">
                                    <input type="radio" name="payment_term" value="full" checked>
                                    <span>Full Payment (1x)</span>
                                </label>
                                <label style="display:inline-flex; align-items:center; gap:6px; cursor:pointer; font-weight:600;">
                                    <input type="radio" name="payment_term" value="3_months">
                                    <span>3 Months Installment (÷ 3)</span>
                                </label>
                                <label style="display:inline-flex; align-items:center; gap:6px; cursor:pointer; font-weight:600;">
                                    <input type="radio" name="payment_term" value="6_months">
                                    <span>6 Months Installment (÷ 6)</span>
                                </label>
                            </div>

                            <!-- Live Installment Formula Display Box -->
                            <div id="portalFormulaDisplay" style="display:none; background:#ffffff; border:1px solid #93c5fd; padding:12px 16px; border-radius:8px;">
                                <div style="display:flex; justify-content:space-between; align-items:center;">
                                    <div>
                                        <span style="font-size:11.5px; color:#64748b; font-weight:600; text-transform:uppercase;">Monthly Payment Calculation</span>
                                        <div style="font-size:18px; font-weight:800; color:#1e40af;" id="portalMonthlyAmount">PHP 0.00 / month</div>
                                    </div>
                                    <div style="font-size:12.5px; font-weight:600; color:#334155;" id="portalTermSummary">
                                        Formula: Total ÷ Months
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PAYMENT METHOD & PARTNER ASSIGNMENT -->
                        <div class="form-grid">
                            <!-- Payment Channel -->
                            <div class="form-group">
                                <label style="font-weight:700;" data-i18n="lbl_payment_method">Payment Channel *</label>
                                <select name="payment_method" id="portalPaymentMethod" class="form-control" style="font-weight:600;">
                                    <option value="gcash">GCash (Ferdenand Sabado - 09617190588)</option>
                                    <option value="cash">Cash Payment</option>
                                </select>
                            </div>

                            <!-- Partner Assignment -->
                            <div class="form-group" style="grid-column: span 2;">
                                <label style="font-weight:700;" data-i18n="lbl_partner_assigned">Partner Assignment (Account Split) *</label>
                                <select name="partner_assigned" id="portalPartnerAssigned" class="form-control" style="font-weight:600;">
                                    <option value="both">Both (Mr. Ibraheem & Mr. Fawas - 50/50 Split)</option>
                                    <option value="ibraheem">Mr. Ibraheem (100% Share)</option>
                                    <option value="fawas">Mr. Fawas (100% Share)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom:16px;">
                            <label>Package Inclusions & Description</label>
                            <textarea name="service_description" id="portalDescription" class="form-control" rows="5">Includes: 1. Web design / Web system
2. Job post / Job Offer
3 Deployment System (QMS)
4. Maintenance System
5. Location Rescue system
6. E-commerce site with SSL Security
7. PRA Deployment
8. FRA/SRA SYSTEM (IF NEEDED)
9. Block Listing for OFW
10. Voucher System
Hosting and domain, with SSL security
--- Nothing Follows ---</textarea>
                        </div>

                        <div class="form-group" style="margin-bottom:24px;">
                            <label>Additional Notes or Instructions</label>
                            <textarea name="notes" id="portalNotes" class="form-control" rows="2" placeholder="Any specific requirements, billing cycle, or PO reference..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" style="padding:12px 28px; font-size:15px;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span data-i18n="btn_submit_request">Submit Invoice Request</span>
                        </button>
                    </form>
                </div>

                <!-- Submission Success Screen -->
                <div class="portal-card" id="portalSuccessSection" style="display:none; text-align:center; padding:40px 24px;">
                    <div style="width:64px; height:64px; background:#dcfce7; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; color:#15803d; margin-bottom:16px;">
                        <svg width="36" height="36" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <h3 style="font-size:24px; font-weight:800; color:var(--text-heading); margin-bottom:6px;">Invoice Successfully Generated!</h3>
                    <p style="color:var(--text-muted); font-size:14px; max-width:540px; margin:0 auto 24px auto;">
                        Your invoice has been recorded in the database with the selected payment terms.
                    </p>

                    <div style="background:var(--bg-muted); border:1px solid var(--border); border-radius:var(--radius-lg); padding:20px; max-width:520px; margin:0 auto 24px auto; text-align:left;">
                        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                            <span style="color:#64748b;">Invoice Number:</span>
                            <strong style="font-family:monospace; font-size:16px; color:var(--primary);" id="portalSuccessInvoiceNum">-</strong>
                        </div>
                        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                            <span style="color:#64748b;">Client Name:</span>
                            <strong id="portalSuccessClient">-</strong>
                        </div>
                        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                            <span style="color:#64748b;">Total Price:</span>
                            <strong id="portalSuccessPrice">-</strong>
                        </div>
                        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                            <span style="color:#64748b;">Payment Plan:</span>
                            <strong style="color:#1e40af;" id="portalSuccessPlan">-</strong>
                        </div>
                        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                            <span style="color:#64748b;">Assigned Partner:</span>
                            <strong id="portalSuccessPartner">-</strong>
                        </div>
                        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                            <span style="color:#64748b;">Remaining Balance:</span>
                            <strong style="color:#b91c1c;" id="portalSuccessBalance">-</strong>
                        </div>
                        <div style="border-top:1px dashed #cbd5e1; padding-top:10px; margin-top:10px; font-size:12px; color:#475569;">
                            <strong>GCash Transfer Info:</strong> Ferdenand Sabado (09617190588)
                        </div>
                    </div>

                    <div style="display:flex; justify-content:center; gap:12px;">
                        <a href="#" id="portalSuccessPrintBtn" target="_blank" class="btn btn-primary" style="padding:10px 20px;">
                            🖨️ View & Print Official Invoice
                        </a>
                        <button class="btn btn-outline" onclick="App.resetPortalForm()">
                            + Create Another Invoice
                        </button>
                    </div>
                </div>

            </section>

            <!-- ==============================================
                 VIEW 5: Products & Services Catalog
                 ============================================== -->
            <section class="view-section" id="view-products">
                <div class="filter-bar" style="justify-content: flex-end;">
                    <button class="btn btn-primary btn-sm" onclick="App.openNewProductModal()">
                        <span>+ </span><span data-i18n="btn_add_product">Add Service</span>
                    </button>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <span data-i18n="title_products">Products & Services Catalog</span>
                        </div>
                    </div>
                    <div class="card-body" style="padding:0;">
                        <div class="table-container" style="border:none;">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th data-i18n="th_item_name">Service / Package Name</th>
                                        <th data-i18n="th_description">Inclusions & Description</th>
                                        <th data-i18n="th_unit_price">Price (PHP)</th>
                                        <th data-i18n="th_tax_rate">Tax %</th>
                                        <th style="width: 140px;" data-i18n="th_actions">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="productsTableBody">
                                    <!-- Dynamic rows loaded via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ==============================================
                 VIEW 6: Settings
                 ============================================== -->
            <section class="view-section" id="view-settings">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span data-i18n="settings_title">Company Information & Billing Profile</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Official Brand Identity Card -->
                        <div style="display:flex; align-items:center; gap:16px; margin-bottom:20px; padding:14px 18px; background:#f8fafc; border:1px solid var(--border); border-radius:8px;">
                            <div style="background:#ffffff; padding:6px 12px; border-radius:8px; border:1px solid #e2e8f0; box-shadow:0 1px 4px rgba(0,0,0,0.06);">
                                <img src="assets/images/yaramay-logo.png" alt="YARAMAY Official Corporate Logo" style="height:56px; width:auto; display:block;">
                            </div>
                            <div>
                                <div style="font-weight:700; font-size:14px; color:var(--text-heading);">Official Corporate Logo & Brand Identity</div>
                                <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">Applied system-wide across Admin Dashboard, Client Invoice Portal, and PDF/Printable Official Invoices.</div>
                            </div>
                        </div>

                        <form id="settingsForm">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label data-i18n="lbl_company_name">Company Name *</label>
                                    <input type="text" name="company_name" id="setting_company_name" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label data-i18n="lbl_company_manager">General Manager Name *</label>
                                    <input type="text" name="company_manager" id="setting_company_manager" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label data-i18n="lbl_company_phone">Contact Numbers (SMS/WhatsApp)</label>
                                    <input type="text" name="company_phone" id="setting_company_phone" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label data-i18n="lbl_company_gcash">GCash Number *</label>
                                    <input type="text" name="company_gcash" id="setting_company_gcash" class="form-control" value="09617190588">
                                </div>
                                <div class="form-group">
                                    <label data-i18n="lbl_gcash_account">GCash Account Name *</label>
                                    <input type="text" name="gcash_account_name" id="setting_gcash_account_name" class="form-control" value="Ferdenand Sabado">
                                </div>
                                <div class="form-group">
                                    <label>Partner 1 Name</label>
                                    <input type="text" name="partner_1_name" id="setting_partner_1_name" class="form-control" value="Mr. Ibraheem">
                                </div>
                                <div class="form-group">
                                    <label>Partner 2 Name</label>
                                    <input type="text" name="partner_2_name" id="setting_partner_2_name" class="form-control" value="Mr. Fawas">
                                </div>
                                <div class="form-group">
                                    <label data-i18n="lbl_company_website">Website</label>
                                    <input type="text" name="company_website" id="setting_company_website" class="form-control">
                                </div>
                            </div>

                            <div class="form-group" style="margin-bottom: 20px;">
                                <label data-i18n="lbl_company_address">Complete Company Address</label>
                                <input type="text" name="company_address" id="setting_company_address" class="form-control">
                            </div>

                            <div class="form-group" style="margin-bottom: 24px;">
                                <label data-i18n="lbl_invoice_footer">Default Payment Notes & Bank/GCash Details</label>
                                <textarea name="invoice_footer" id="setting_invoice_footer" class="form-control" rows="3"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span data-i18n="btn_save_settings">Save Settings</span>
                            </button>
                        </form>
                    </div>
                </div>
            </section>

            <!-- ==============================================
                 VIEW 7: Backup & Restore
                 ============================================== -->
            <section class="view-section" id="view-backup">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            <span data-i18n="title_backup">Data Backup & Recovery</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 260px), 1fr)); gap:24px;">
                            <div style="background:var(--primary-light); border:1px solid var(--primary-border); padding:24px; border-radius:var(--radius-lg);">
                                <h3 style="color:var(--primary-dark); font-size:17px; margin-bottom:8px;" data-i18n="backup_export_title">Export Full Database Backup</h3>
                                <p style="color:var(--text-muted); font-size:13px; margin-bottom:16px;" data-i18n="backup_export_desc">Download a complete JSON snapshot containing all clients, invoices, service items, and company settings.</p>
                                <a href="api.php?action=export_backup" class="btn btn-primary">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    <span data-i18n="btn_download_backup">Download Backup (JSON)</span>
                                </a>
                            </div>

                            <div style="background:var(--bg-muted); border:1px solid var(--border); padding:24px; border-radius:var(--radius-lg);">
                                <h3 style="font-size:17px; margin-bottom:8px;" data-i18n="backup_import_title">Restore Database Backup</h3>
                                <p style="color:var(--text-muted); font-size:13px; margin-bottom:16px;" data-i18n="backup_import_desc">Upload a previously exported JSON backup to restore all data.</p>
                                <form action="api.php" method="POST" enctype="multipart/form-data" onsubmit="return confirm(App.t('confirm_restore'));">
                                    <input type="hidden" name="action" value="import_backup">
                                    <input type="file" name="backup_file" accept=".json" class="form-control" style="margin-bottom:12px;" required>
                                    <button type="submit" class="btn btn-outline" style="border-color:var(--primary); color:var(--primary);">
                                        <span data-i18n="btn_restore_now">Restore Backup Now</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ==============================================
                 VIEW 8: Admin Users Management
                 ============================================== -->
            <section class="view-section" id="view-users">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <span data-i18n="title_users">Admin Users & Access Control</span>
                        </div>
                        <button class="btn btn-primary btn-sm" onclick="App.openNewUserModal()">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span data-i18n="btn_add_user">+ Add New Admin User</span>
                        </button>
                    </div>
                    <div class="card-body">
                        <div style="background:var(--primary-light); border:1px solid var(--primary-border); padding:12px 18px; border-radius:8px; margin-bottom:18px; font-size:13px; color:var(--primary-dark); display:flex; align-items:center; gap:12px;">
                            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <div>
                                Superadmin account: <strong>ibraheem01</strong>. Admins can add new administrator users, change passwords, and edit usernames.
                            </div>
                        </div>

                        <div class="table-container">
                            <table class="data-table" id="usersTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Full Name</th>
                                        <th>Role / Privilege</th>
                                        <th>Email</th>
                                        <th>Created Date</th>
                                        <th style="text-align:right;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="usersTableBody">
                                    <!-- Populated by JavaScript App.loadUsers() -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ==============================================
                 VIEW 9: Security Shield & Threat Intelligence
                 ============================================== -->
            <section class="view-section" id="view-security">
                <!-- Security KPIs -->
                <div class="kpi-grid" style="margin-bottom:24px;">
                    <div class="kpi-card" style="border-left: 4px solid #16a34a;">
                        <div class="kpi-content">
                            <h3 style="color:#16a34a;">Firewall Shield Status</h3>
                            <div class="kpi-value" style="color:#16a34a; font-size:20px;">ACTIVE & PROTECTED</div>
                            <span class="kpi-sub">WAF, Anti-Brute-Force & Anti-Virus</span>
                        </div>
                        <div class="kpi-icon" style="background:#dcfce7; color:#16a34a;">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                    </div>

                    <div class="kpi-card total">
                        <div class="kpi-content">
                            <h3>Attacks Blocked</h3>
                            <div class="kpi-value" id="kpiAttacksBlocked" style="color:#dc2626;">0</div>
                            <span class="kpi-sub">SQLi, XSS, RCE & Directory Traversal</span>
                        </div>
                        <div class="kpi-icon" style="background:#fee2e2; color:#dc2626;">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        </div>
                    </div>

                    <div class="kpi-card balance">
                        <div class="kpi-content">
                            <h3>Failed Logins Tracked</h3>
                            <div class="kpi-value" id="kpiFailedLogins">0</div>
                            <span class="kpi-sub">Monitored for brute-force threats</span>
                        </div>
                        <div class="kpi-icon">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                    </div>

                    <div class="kpi-card paid">
                        <div class="kpi-content">
                            <h3>Lockouts Triggered</h3>
                            <div class="kpi-value" id="kpiLockoutsTriggered">0</div>
                            <span class="kpi-sub">IPs blocked after 5 failed attempts</span>
                        </div>
                        <div class="kpi-icon">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                    </div>
                </div>

                <!-- Active Security Defenses Grid -->
                <div class="card" style="margin-bottom:24px;">
                    <div class="card-header">
                        <div class="card-title">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Active System Defenses & Security Configuration</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 260px), 1fr)); gap:16px;">
                            <div style="background:#f8fafc; border:1px solid #cbd5e1; padding:14px; border-radius:8px; border-left:4px solid #16a34a;">
                                <strong style="color:#0f172a; font-size:13.5px; display:block; margin-bottom:4px;">🛡️ Web Application Firewall (WAF)</strong>
                                <span style="font-size:12px; color:#475569;">Scans every request for SQL injection, Cross-Site Scripting (XSS), Remote Code Execution (RCE), command injection, and null bytes.</span>
                            </div>
                            <div style="background:#f8fafc; border:1px solid #cbd5e1; padding:14px; border-radius:8px; border-left:4px solid #16a34a;">
                                <strong style="color:#0f172a; font-size:13.5px; display:block; margin-bottom:4px;">🔒 Anti-Brute-Force & Lockout Shield</strong>
                                <span style="font-size:12px; color:#475569;">Automatically blocks and temporarily locks any IP address or username after 5 failed login attempts for 15 minutes.</span>
                            </div>
                            <div style="background:#f8fafc; border:1px solid #cbd5e1; padding:14px; border-radius:8px; border-left:4px solid #16a34a;">
                                <strong style="color:#0f172a; font-size:13.5px; display:block; margin-bottom:4px;">🦠 Anti-Malware Upload Scanner</strong>
                                <span style="font-size:12px; color:#475569;">Inspects all uploaded backup files to detect and block web shells, PHP executable tags, eval scripts, and image polyglot attacks.</span>
                            </div>
                            <div style="background:#f8fafc; border:1px solid #cbd5e1; padding:14px; border-radius:8px; border-left:4px solid #16a34a;">
                                <strong style="color:#0f172a; font-size:13.5px; display:block; margin-bottom:4px;">🚫 Database Direct-Access Blocker</strong>
                                <span style="font-size:12px; color:#475569;">Apache/IIS rules prevent external users from directly browsing or downloading <code>invoices.db</code>, <code>.sql</code> dumps, or session files.</span>
                            </div>
                            <div style="background:#f8fafc; border:1px solid #cbd5e1; padding:14px; border-radius:8px; border-left:4px solid #16a34a;">
                                <strong style="color:#0f172a; font-size:13.5px; display:block; margin-bottom:4px;">⚡ Anti-Spam & DoS Rate Limiter</strong>
                                <span style="font-size:12px; color:#475569;">Restricts submission bursts to prevent automated bots and denial-of-service spam on public client invoice portals.</span>
                            </div>
                            <div style="background:#f8fafc; border:1px solid #cbd5e1; padding:14px; border-radius:8px; border-left:4px solid #16a34a;">
                                <strong style="color:#0f172a; font-size:13.5px; display:block; margin-bottom:4px;">🔐 Security Headers (CSP & Anti-Clickjack)</strong>
                                <span style="font-size:12px; color:#475569;">Enforces <code>X-Frame-Options</code>, <code>X-Content-Type-Options: nosniff</code>, <code>Referrer-Policy</code>, and <code>Content-Security-Policy</code>.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Live Security Incident & Audit Logs Table -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Security Incident & Audit Logs (Real-Time)</span>
                        </div>
                        <div style="display:flex; gap:8px;">
                            <button class="btn btn-outline btn-sm" onclick="App.loadSecurityLogs()">
                                🔄 Refresh Logs
                            </button>
                            <button class="btn btn-outline btn-sm" onclick="App.clearSecurityLogs()" style="color:#dc2626; border-color:#fca5a5;">
                                🗑️ Clear Logs
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-container">
                            <table class="data-table" id="securityLogsTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Event Type</th>
                                        <th>IP Address</th>
                                        <th>Account</th>
                                        <th>Incident Details</th>
                                        <th>User Agent</th>
                                        <th>Timestamp</th>
                                    </tr>
                                </thead>
                                <tbody id="securityLogsTableBody">
                                    <!-- Populated by JavaScript App.loadSecurityLogs() -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

        </main>
    </div>

</div>

<!-- ==============================================
     MODAL 1: Create / Edit Invoice Modal
     ============================================== -->
<div class="modal-overlay" id="invoiceModal">
    <div class="modal-dialog" style="max-width: 980px;">
        <div class="modal-header">
            <h3 id="invoiceFormTitle" data-i18n="btn_new_invoice">New Invoice</h3>
            <button class="modal-close" onclick="App.closeModal('invoiceModal')">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="invoiceForm">
            <div class="modal-body">
                <input type="hidden" id="invoiceIdInput">

                <!-- Client Selector & Quick Add -->
                <div style="background:var(--bg-muted); border:1px solid var(--border); padding:16px; border-radius:var(--radius-md); margin-bottom:18px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                        <label style="font-weight:700; color:var(--text-heading);" data-i18n="lbl_client">Client Account *</label>
                        <label style="font-size:12.5px; display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
                            <input type="checkbox" id="toggleNewClientCheck">
                            <span data-i18n="lbl_or_new_client">+ Quick Create New Client</span>
                        </label>
                    </div>

                    <div id="existingClientGroup">
                        <select id="invoiceClientSelect" class="form-control">
                            <!-- Populated by JS -->
                        </select>
                    </div>

                    <div id="quickNewClientBox" style="display:none; margin-top:10px;">
                        <div class="form-grid">
                            <input type="text" id="quickNewClientName" class="form-control" placeholder="Company / Client Name *">
                            <input type="text" id="quickNewContactPerson" class="form-control" placeholder="Contact Person (e.g. Mr. Allan Rom) *">
                            <input type="text" id="quickNewPhone" class="form-control" placeholder="Phone / Mobile">
                            <input type="text" id="quickNewAddress" class="form-control" placeholder="Address / Location">
                        </div>
                    </div>
                </div>

                <!-- Invoice Meta Row -->
                <div class="form-grid">
                    <div class="form-group">
                        <label data-i18n="lbl_invoice_num">Invoice Number *</label>
                        <input type="text" id="invoiceNumberInput" class="form-control" style="font-family: monospace; font-weight:700;" required>
                    </div>

                    <div class="form-group">
                        <label data-i18n="lbl_issue_date">Issue Date *</label>
                        <input type="date" id="invoiceIssueDate" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label data-i18n="lbl_due_date">Due Date</label>
                        <input type="date" id="invoiceDueDate" class="form-control">
                    </div>

                    <div class="form-group">
                        <label data-i18n="lbl_status">Invoice Status *</label>
                        <select id="invoiceStatusSelect" class="form-control">
                            <option value="paid" data-i18n="status_paid">Paid in Full</option>
                            <option value="partial" data-i18n="status_partial">Partial Payment</option>
                            <option value="unpaid" data-i18n="status_unpaid">Unpaid (Due)</option>
                            <option value="pending" data-i18n="status_pending">Pending</option>
                            <option value="cancelled" data-i18n="status_cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>

                <!-- Installment Plan & Partner Assignment Row -->
                <div class="form-grid" style="background:#f0f9ff; border:1px solid #bae6fd; padding:12px; border-radius:8px; margin-bottom:16px;">
                    <div class="form-group">
                        <label data-i18n="lbl_payment_term">Payment Plan *</label>
                        <select id="invoicePaymentTerm" class="form-control">
                            <option value="full">Full Payment (1x)</option>
                            <option value="3_months">3 Months Installment</option>
                            <option value="6_months">6 Months Installment</option>
                            <option value="custom">Custom Term (Months)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Installment Months (N)</label>
                        <input type="number" min="1" id="invoiceInstallmentMonths" class="form-control" value="1">
                    </div>

                    <div class="form-group">
                        <label data-i18n="lbl_partner_assigned">Partner Share Assignment *</label>
                        <select id="invoicePartnerAssigned" class="form-control">
                            <option value="both">Both (Mr. Ibraheem & Mr. Fawas - 50/50 Split)</option>
                            <option value="ibraheem">Mr. Ibraheem (100% Share)</option>
                            <option value="fawas">Mr. Fawas (100% Share)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label data-i18n="lbl_payment_method">Payment Channel *</label>
                        <select id="invoicePaymentMethod" class="form-control">
                            <option value="gcash">GCash (Ferdenand Sabado 09617190588)</option>
                            <option value="cash">Cash Payment</option>
                            <option value="stc_pay">STC Pay</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>
                </div>

                <!-- Price Notes -->
                <div class="form-grid">
                    <div class="form-group">
                        <label data-i18n="lbl_price_note">Price Note / Formula</label>
                        <input type="text" id="invoicePriceNote" class="form-control" placeholder="e.g. PHP 20,000 / 3 Months = PHP 6,666.67 / Month">
                    </div>

                    <div class="form-group">
                        <label data-i18n="lbl_amount_due_note">Amount Due Note</label>
                        <input type="text" id="invoiceAmountDueNote" class="form-control" placeholder="e.g. 7,500 PHP or Monthly Installment">
                    </div>
                </div>

                <!-- Quick Load Service Package Preset -->
                <div style="background:#f0f9ff; border:1px solid #bae6fd; border-radius:8px; padding:10px 14px; margin-bottom:14px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span style="font-size:12.5px; font-weight:700; color:#0369a1;">⚡ Quick Load Official Service Package & Pricing Preset:</span>
                    </div>
                    <select id="adminPackagePreset" onchange="App.applyAdminPackagePreset(this.value)" style="padding:6px 12px; font-size:12.5px; font-weight:700; border-radius:6px; border:1.5px solid #0284c7; background:#ffffff; color:#0369a1; cursor:pointer;">
                        <option value="">-- Select Package Preset from Official Contracts --</option>
                        <option value="pkg_jobconnection">The Jobconnection: E-Commerce & Web Maintenance (QMS) — ₱7,500.00 / mo</option>
                        <option value="pkg_brilliant">Brilliant Minds: Full Annual Enterprise Contract — ₱90,000.00 / yr</option>
                        <option value="pkg_joseline">Joseline Manpower: Hardware & E-mail Server Maintenance — ₱5,500.00 / mo</option>
                        <option value="pkg_simsoh">Simsoh Agency: Web and System (Online) Maintenance — ₱5,000.00 / mo</option>
                    </select>
                </div>

                <!-- Invoice Line Items Table -->
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:20px; margin-bottom:8px;">
                    <h4 style="color:var(--primary-dark); font-size:15px; font-weight:800;" data-i18n="lbl_invoice_items">Invoice Line Items & Services</h4>
                    <button type="button" class="btn btn-sm btn-light" id="addItemBtn">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span data-i18n="btn_add_item">Add Item</span>
                    </button>
                </div>

                <div class="items-table-wrapper">
                    <table class="items-builder-table">
                        <thead>
                            <tr>
                                <th style="width: 44%;" data-i18n="th_item_name">Service / Inclusions</th>
                                <th style="width: 10%; text-align:center;" data-i18n="th_quantity">Qty</th>
                                <th style="width: 15%;" data-i18n="th_unit_price">Unit Price</th>
                                <th style="width: 10%;" data-i18n="th_discount">Discount</th>
                                <th style="width: 8%; text-align:center;" data-i18n="th_tax_rate">Tax %</th>
                                <th style="width: 13%; text-align:center;" data-i18n="th_price">Total</th>
                                <th style="width: 35px;"></th>
                            </tr>
                        </thead>
                        <tbody id="invoiceItemsTableBody">
                            <!-- Dynamic Item Rows -->
                        </tbody>
                    </table>
                </div>

                <!-- Pricing, Paid Amount & Remaining Balance -->
                <div style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:20px; margin-top:16px;">
                    <div style="flex:1; min-width:280px;">
                        <label style="font-size:13px; font-weight:700; color:var(--text-heading); margin-bottom:6px; display:block;" data-i18n="lbl_notes_terms">Payment Transfer Instructions & Notes</label>
                        <textarea id="invoiceNotesInput" class="form-control" rows="4"></textarea>
                    </div>

                    <div class="invoice-calc-box" style="flex:1; min-width:280px; max-width:380px;">
                        <div class="calc-row">
                            <span data-i18n="calc_subtotal">Subtotal:</span>
                            <span id="calcSubtotal">0.00 PHP</span>
                        </div>
                        <div class="calc-row">
                            <span data-i18n="calc_discount">Total Discount:</span>
                            <span id="calcDiscount" style="color:#dc2626;">0.00 PHP</span>
                        </div>
                        <div class="calc-row">
                            <span data-i18n="calc_tax">VAT / Tax:</span>
                            <span id="calcTax">0.00 PHP</span>
                        </div>
                        <div class="calc-row grand">
                            <span data-i18n="calc_grand_total">Total Cost / Price:</span>
                            <span id="calcGrandTotal">0.00 PHP</span>
                        </div>
                        <div class="calc-row" style="background:#eff6ff; font-weight:700; color:#1e40af;">
                            <span data-i18n="calc_monthly">Monthly Installment:</span>
                            <span id="calcMonthlyPayment">0.00 PHP</span>
                        </div>
                        <div class="calc-row" style="background:#fef2f2; font-weight:700; color:#b91c1c;">
                            <span data-i18n="lbl_remaining_balance">Remaining Balance:</span>
                            <span id="calcRemainingBalance">0.00 PHP</span>
                        </div>

                        <!-- Amount Paid & Remaining Balance Fields -->
                        <div style="margin-top:12px; padding-top:12px; border-top:1px dashed #cbd5e1;">
                            <div class="form-group" style="margin-bottom:8px;">
                                <label style="font-size:12.5px; font-weight:700; color:#15803d;" data-i18n="lbl_paid_amount">Amount Paid (PHP)</label>
                                <input type="number" step="0.01" min="0" id="invoicePaidAmountInput" class="form-control" value="0.00">
                            </div>
                            <div class="form-group">
                                <label style="font-size:12.5px; font-weight:700; color:#b91c1c;" data-i18n="lbl_remaining_balance">Remaining Balance (PHP)</label>
                                <input type="number" step="0.01" min="0" id="invoiceRemainingBalanceInput" class="form-control" value="0.00">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="App.closeModal('invoiceModal')" data-i18n="btn_cancel">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span data-i18n="btn_save_invoice">Save & Confirm Invoice</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==============================================
     MODAL 2: Quick Record Payment Modal
     ============================================== -->
<div class="modal-overlay" id="quickPaymentModal">
    <div class="modal-dialog modal-sm">
        <div class="modal-header">
            <h3 data-i18n="btn_record_payment">Record Payment & Update Balance</h3>
            <button class="modal-close" onclick="App.closeModal('quickPaymentModal')">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="quickPaymentForm">
            <div class="modal-body">
                <input type="hidden" name="id" id="quickPayInvoiceId">

                <div style="background:var(--bg-muted); border:1px solid var(--border); padding:14px; border-radius:8px; margin-bottom:16px;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span style="font-size:12px; color:#64748b;">Invoice #:</span>
                        <strong style="font-family:monospace; color:var(--primary);" id="quickPayInvoiceNum">-</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span style="font-size:12px; color:#64748b;">Client:</span>
                        <strong id="quickPayClientName">-</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span style="font-size:12px; color:#64748b;">Total Price:</span>
                        <strong id="quickPayGrandTotal">-</strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span style="font-size:12px; color:#64748b;">Current Paid:</span>
                        <span style="color:#15803d; font-weight:700;" id="quickPayCurrentPaid">-</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span style="font-size:12px; color:#64748b;">Current Balance:</span>
                        <span style="color:#b91c1c; font-weight:700;" id="quickPayCurrentBalance">-</span>
                    </div>
                    <div style="border-top:1px dashed #cbd5e1; padding-top:6px; margin-top:6px; display:flex; justify-content:space-between;">
                        <span style="font-size:12px; color:#64748b;">Partner Assignment:</span>
                        <span style="font-weight:700; color:#1e40af;" id="quickPayPartnerLabel">-</span>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:14px;">
                    <label style="font-weight:700;">Payment Amount to Collect (PHP) *</label>
                    <input type="number" step="0.01" min="0.01" name="payment_amount" id="quickPayAmountInput" class="form-control" required>
                </div>

                <div class="form-group" style="margin-bottom:14px;">
                    <label data-i18n="lbl_payment_method">Payment Method *</label>
                    <select name="payment_method" id="quickPayMethodSelect" class="form-control">
                        <option value="gcash">GCash (Ferdenand Sabado - 09617190588)</option>
                        <option value="cash">Cash Payment</option>
                        <option value="stc_pay">STC Pay</option>
                        <option value="bank_transfer">Bank Transfer</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Payment Reference / Receipt Note</label>
                    <input type="text" name="payment_note" id="quickPayNoteInput" class="form-control" placeholder="e.g. GCash Ref #, Bank Auth Code, or Check #">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="App.closeModal('quickPaymentModal')" data-i18n="btn_cancel">Cancel</button>
                <button type="submit" class="btn btn-primary" data-i18n="btn_record_payment">Record Payment</button>
            </div>
        </form>
    </div>
</div>

<!-- ==============================================
     MODAL 3: Client Dossier & Statement Modal
     ============================================== -->
<div class="modal-overlay" id="clientDossierModal">
    <div class="modal-dialog" style="max-width: 880px;">
        <div class="modal-header">
            <h3 data-i18n="client_info_title">Client Information & Invoices Dossier</h3>
            <button class="modal-close" onclick="App.closeModal('clientDossierModal')">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-body" id="clientDossierContent">
            <!-- Dynamic dossier loaded via JS -->
        </div>
    </div>
</div>

<!-- ==============================================
     MODAL 4: Client Form Modal
     ============================================== -->
<div class="modal-overlay" id="clientModal">
    <div class="modal-dialog modal-sm">
        <div class="modal-header">
            <h3 id="clientModalTitle" data-i18n="client_modal_title_new">Add New Client</h3>
            <button class="modal-close" onclick="App.closeModal('clientModal')">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="clientForm">
            <div class="modal-body">
                <input type="hidden" id="clientIdInput" name="id">
                <div class="form-group" style="margin-bottom:14px;">
                    <label data-i18n="lbl_client_name">Company / Organization Name *</label>
                    <input type="text" id="clientNameInput" name="name" class="form-control" placeholder="e.g. Brilliant Minds Inc." required>
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label data-i18n="lbl_contact_person">Contact Person *</label>
                    <input type="text" id="clientContactPersonInput" name="contact_person" class="form-control" placeholder="e.g. Ms. Pilar R. Corporal" required>
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label data-i18n="lbl_client_phone">Phone / Mobile</label>
                    <input type="text" id="clientPhoneInput" name="phone" class="form-control" placeholder="+63 907 525 1376">
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label data-i18n="lbl_client_email">Email Address</label>
                    <input type="email" id="clientEmailInput" name="email" class="form-control">
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label data-i18n="lbl_client_tax">TIN / Tax Number</label>
                    <input type="text" id="clientTaxInput" name="tax_number" class="form-control">
                </div>
                <div class="form-group">
                    <label data-i18n="lbl_client_address">Complete Address</label>
                    <input type="text" id="clientAddressInput" name="address" class="form-control" placeholder="e.g. Midland Plaza Adriatico Manila">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="App.closeModal('clientModal')" data-i18n="btn_cancel">Cancel</button>
                <button type="submit" class="btn btn-primary" data-i18n="btn_save_client">Save Client</button>
            </div>
        </form>
    </div>
</div>

<!-- ==============================================
     MODAL 5: Product / Service Form Modal
     ============================================== -->
<div class="modal-overlay" id="productModal">
    <div class="modal-dialog modal-sm">
        <div class="modal-header">
            <h3 id="productModalTitle" data-i18n="product_modal_title_new">Add New Service</h3>
            <button class="modal-close" onclick="App.closeModal('productModal')">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="productForm">
            <div class="modal-body">
                <input type="hidden" id="productIdInput" name="id">
                <div class="form-group" style="margin-bottom:14px;">
                    <label data-i18n="lbl_product_name">Service / Package Name *</label>
                    <input type="text" id="productNameInput" name="name" class="form-control" required>
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label data-i18n="lbl_product_price">Default Price (PHP) *</label>
                    <input type="number" step="0.01" min="0" id="productPriceInput" name="price" class="form-control" required>
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label data-i18n="lbl_product_tax">Tax Rate (%)</label>
                    <input type="number" step="0.01" min="0" id="productTaxInput" name="tax_rate" class="form-control" value="0">
                </div>
                <div class="form-group">
                    <label data-i18n="lbl_product_desc">Inclusions & Description</label>
                    <textarea id="productDescInput" name="description" class="form-control" rows="4"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="App.closeModal('productModal')" data-i18n="btn_cancel">Cancel</button>
                <button type="submit" class="btn btn-primary" data-i18n="btn_save_product">Save Service</button>
            </div>
        </form>
    </div>
</div>

<!-- ==============================================
     MODAL 6: View Invoice Modal
     ============================================== -->
<div class="modal-overlay" id="viewInvoiceModal">
    <div class="modal-dialog" style="max-width: 800px;">
        <div class="modal-header">
            <h3 data-i18n="btn_view">Invoice Preview</h3>
            <button class="modal-close" onclick="App.closeModal('viewInvoiceModal')">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-body" id="viewInvoiceModalContent">
            <!-- Dynamic view injected via JS -->
        </div>
    </div>
</div>

<!-- ==============================================
     MODAL 7: Add / Edit Admin User Modal
     ============================================== -->
<div class="modal-overlay" id="userModal">
    <div class="modal-dialog modal-sm" style="max-width: 500px;">
        <div class="modal-header">
            <h3 id="userModalTitle">Add New Admin User</h3>
            <button class="modal-close" onclick="App.closeModal('userModal')">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="userForm" onsubmit="App.saveUser(event)">
            <div class="modal-body">
                <input type="hidden" id="userIdInput" name="id">
                <div class="form-group" style="margin-bottom:14px;">
                    <label style="font-weight:700;">Admin Username *</label>
                    <input type="text" id="userUsernameInput" name="username" class="form-control" placeholder="e.g. ibraheem01" required>
                    <small style="color:var(--text-muted); font-size:11.5px;">Username used to login to the admin dashboard.</small>
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label style="font-weight:700;">Full Name *</label>
                    <input type="text" id="userFullNameInput" name="full_name" class="form-control" placeholder="e.g. Mr. Ibraheem" required>
                </div>
                <div class="form-grid" style="margin-bottom:14px;">
                    <div class="form-group">
                        <label style="font-weight:700;">Role / Privilege *</label>
                        <select id="userRoleSelect" name="role" class="form-control" style="font-weight:600;">
                            <option value="admin">Admin</option>
                            <option value="superadmin">Superadmin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" id="userEmailInput" name="email" class="form-control" placeholder="admin@yaramay.online">
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label style="font-weight:700;" id="userPasswordLabel">Password *</label>
                    <input type="password" id="userPasswordInput" name="password" class="form-control" placeholder="Enter password (min 6 characters)">
                    <small style="color:var(--text-muted); font-size:11.5px;" id="userPasswordHint">Minimum 6 characters. Leave blank when editing to keep current password.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="App.closeModal('userModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" id="saveUserBtn">Save Admin User</button>
            </div>
        </form>
    </div>
</div>

<!-- ==============================================
     MODAL 8: Change My Password Modal
     ============================================== -->
<div class="modal-overlay" id="changeMyPasswordModal">
    <div class="modal-dialog modal-sm" style="max-width: 440px;">
        <div class="modal-header">
            <h3>🔑 Change My Password</h3>
            <button class="modal-close" onclick="App.closeModal('changeMyPasswordModal')">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="changeMyPasswordForm" onsubmit="App.submitChangeMyPassword(event)">
            <div class="modal-body">
                <div class="form-group" style="margin-bottom:14px;">
                    <label style="font-weight:700;">Current Password *</label>
                    <input type="password" id="myCurrentPasswordInput" class="form-control" placeholder="Enter current password" required>
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label style="font-weight:700;">New Password *</label>
                    <input type="password" id="myNewPasswordInput" class="form-control" placeholder="Enter new password (min 6 characters)" required minlength="6">
                </div>
                <div class="form-group" style="margin-bottom:14px;">
                    <label style="font-weight:700;">Confirm New Password *</label>
                    <input type="password" id="myConfirmPasswordInput" class="form-control" placeholder="Re-enter new password" required minlength="6">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="App.closeModal('changeMyPasswordModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Password</button>
            </div>
        </form>
    </div>
</div>

<!-- ==============================================
     MODAL 9: Quick Change Partner Assignment Modal
     ============================================== -->
<div class="modal-overlay" id="partnerModal">
    <div class="modal-dialog modal-sm" style="max-width: 480px;">
        <div class="modal-header">
            <h3>🤝 <span id="partnerModalTitle">Change Assigned Partner</span></h3>
            <button class="modal-close" onclick="App.closeModal('partnerModal')">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="quickPartnerInvoiceId">
            <p style="font-size:13px; color:#475569; margin-bottom:16px; line-height:1.5;">
                Assign revenue division for Invoice <strong id="quickPartnerInvoiceNum" style="color:var(--primary); font-family:monospace; font-size:14px;">#00000</strong>. When updated, collected payments will automatically divide according to the selected partner account.
            </p>
            <div style="display:flex; flex-direction:column; gap:10px;">
                <label style="display:flex; align-items:center; gap:12px; padding:12px 14px; border:1.5px solid #e2e8f0; border-radius:10px; cursor:pointer; background:#ffffff; transition:all 0.2s;" id="labelPartnerBoth">
                    <input type="radio" name="quickPartnerChoice" value="both" style="width:18px; height:18px; accent-color:#1e3a8a;">
                    <div>
                        <strong style="color:#0f172a; display:block; font-size:13.5px;">🤝 Both (Mr. Ibraheem & Mr. Fawas - 50/50 Split)</strong>
                        <span style="font-size:11.5px; color:#64748b;">Payments divided equally between Mr. Ibraheem and Mr. Fawas.</span>
                    </div>
                </label>
                <label style="display:flex; align-items:center; gap:12px; padding:12px 14px; border:1.5px solid #e2e8f0; border-radius:10px; cursor:pointer; background:#ffffff; transition:all 0.2s;" id="labelPartnerIbraheem">
                    <input type="radio" name="quickPartnerChoice" value="ibraheem" style="width:18px; height:18px; accent-color:#1e3a8a;">
                    <div>
                        <strong style="color:#1e40af; display:block; font-size:13.5px;">👤 Mr. Ibraheem (100% Share)</strong>
                        <span style="font-size:11.5px; color:#64748b;">All collected payments credited 100% to Mr. Ibraheem.</span>
                    </div>
                </label>
                <label style="display:flex; align-items:center; gap:12px; padding:12px 14px; border:1.5px solid #e2e8f0; border-radius:10px; cursor:pointer; background:#ffffff; transition:all 0.2s;" id="labelPartnerFawas">
                    <input type="radio" name="quickPartnerChoice" value="fawas" style="width:18px; height:18px; accent-color:#7e22ce;">
                    <div>
                        <strong style="color:#7e22ce; display:block; font-size:13.5px;">👤 Mr. Fawas (100% Share)</strong>
                        <span style="font-size:11.5px; color:#64748b;">All collected payments credited 100% to Mr. Fawas.</span>
                    </div>
                </label>
            </div>
        </div>
        <div class="modal-footer" style="display:flex; justify-content:flex-end; gap:10px;">
            <button type="button" class="btn btn-outline" onclick="App.closeModal('partnerModal')">Cancel</button>
            <button type="button" class="btn btn-primary" id="saveQuickPartnerBtn" onclick="App.savePartnerAssignment()">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Save Assignment</span>
            </button>
        </div>
    </div>
</div>

<!-- Toast Notification Container -->
<div class="toast-container" id="toastContainer"></div>

<!-- JavaScript Core -->
<script src="assets/js/app.js"></script>

</body>
</html>
