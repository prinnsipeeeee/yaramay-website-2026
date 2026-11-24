-- ========================================================
-- YARAMAY Computer Maintenance Services
-- Official Invoice Record System Database Dump
-- MySQL / MariaDB Compatible
-- ========================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for settings
-- ----------------------------
CREATE TABLE IF NOT EXISTS `settings` (
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings` (`key`, `value`) VALUES
('company_name', 'YARAMAY Computer Maintenance Services'),
('company_cr', 'Cambridge C5 -1G'),
('company_vat', 'VAT-PH-09617190588'),
('company_phone', '+639075251376 (SMS) / +966508624264'),
('company_email', 'billing@yaramay.online'),
('company_address', '#19 Street Brgy. San Andres Cainta Rizal Cambridge C5 -1G'),
('company_manager', 'Ferdenand (Ibraheem) Sabado'),
('company_title', 'General Manager'),
('company_cert', 'MCP, MCSE, MCSA and CISCO Certified'),
('company_website', 'www.yaramay.online'),
('company_gcash', '09617190588'),
('gcash_account_name', 'Ferdenand Sabado'),
('partner_1_name', 'Mr. Ibraheem'),
('partner_2_name', 'Mr. Fawas'),
('company_stc', 'STC Pay / GCash'),
('currency', 'PHP'),
('currency_symbol', '₱'),
('default_tax_rate', '0'),
('invoice_prefix', 'INV-'),
('invoice_footer', 'We are open for your suggestions on improving our services with you.\nCan the payment be transferred through STC Pay or Gcash and Gcash # 09617190588.\nThanking you for doing Business with us!')
ON DUPLICATE KEY UPDATE `value`=VALUES(`value`);

-- ----------------------------
-- Table structure for users (Admin authentication)
-- ----------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `role` enum('superadmin','admin') NOT NULL DEFAULT 'admin',
  `email` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `email`, `created_at`) VALUES
(1, 'ibraheem01', '$2y$10$o8Wdt8tnTHgbrRQqLzsbLeBAHDWZm3djlxNWNukqINxA.ZmXVsVie', 'Mr. Ibraheem (Superadmin)', 'superadmin', 'ibraheem@yaramay.online', CURRENT_TIMESTAMP)
ON DUPLICATE KEY UPDATE `full_name`=VALUES(`full_name`);

-- ----------------------------
-- Table structure for clients
-- ----------------------------
CREATE TABLE IF NOT EXISTS `clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `tax_number` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `clients` (`id`, `name`, `contact_person`, `phone`, `email`, `address`, `tax_number`, `created_at`) VALUES
(1, 'Brilliant Minds Inc.', 'Ms. Pilar R. Corporal', '+639075251376', 'contact@brilliantminds.ph', 'Midland Plaza Adriatico Manila', 'TIN-BM-00364', CURRENT_TIMESTAMP),
(2, 'The Jobconnection Manpower', 'Mr. Allan Rom', '+639075251376', 'allan.rom@jobconnection.com.ph', 'Ped Gil . Manila', 'TIN-JC-00372', CURRENT_TIMESTAMP),
(3, 'Simsoh Agency', 'Mr. Allan Rom', '+639075251376', 'info@simsoh-agency.com', 'Manila', 'TIN-SA-00355', CURRENT_TIMESTAMP),
(4, 'Joseline International Manpower Corporation', 'Mr. Elmer Aquino', '+639075251376', 'elmer.aquino@joseline-manpower.ph', 'Brgy, Bakal Makati City', 'TIN-JI-00369', CURRENT_TIMESTAMP)
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `contact_person`=VALUES(`contact_person`);

-- ----------------------------
-- Table structure for products
-- ----------------------------
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`id`, `name`, `price`, `tax_rate`, `description`, `created_at`) VALUES
(1, 'Package of E-Commerce Maintenance Services and Web maintenance including Rescue System', 7500.00, 0.00, 'Includes 10-point maintenance package: Web design/system, Job post/offer, Deployment system (QMS), Maintenance system, Location rescue, SSL security, PRA deployment, FRA/SRA system, OFW block listing, Voucher system, Hosting and domain', CURRENT_TIMESTAMP),
(2, 'Package of E-Commerce Maintenance & Web Maintenance (Annual Contract)', 90000.00, 0.00, 'Full annual enterprise coverage with monthly payment option (PHP 7,500/month or PHP 90,000/year)', CURRENT_TIMESTAMP),
(3, 'Web and System (Online) & Maintenance Services', 5000.00, 0.00, 'Monthly online system support, server monitoring, bug fixes, and continuous maintenance', CURRENT_TIMESTAMP),
(4, 'Hardware & Server Maintenance Technical Support', 5500.00, 0.00, 'Hardware maintenance, company email server maintenance, and remote rescue services', CURRENT_TIMESTAMP)
ON DUPLICATE KEY UPDATE `price`=VALUES(`price`);

-- ----------------------------
-- Table structure for invoices
-- ----------------------------
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(50) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `issue_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('paid','partial','unpaid','pending','cancelled') NOT NULL DEFAULT 'unpaid',
  `payment_method` varchar(50) NOT NULL DEFAULT 'gcash',
  `payment_term` varchar(50) NOT NULL DEFAULT 'full',
  `installment_months` int(11) NOT NULL DEFAULT 1,
  `monthly_payment` decimal(10,2) NOT NULL DEFAULT 0.00,
  `partner_assigned` varchar(50) NOT NULL DEFAULT 'both',
  `ibraheem_share` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fawas_share` decimal(10,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `remaining_balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `price_note` text DEFAULT NULL,
  `amount_due_note` text DEFAULT NULL,
  `package_includes` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payment_receipt` varchar(255) DEFAULT NULL,
  `submitted_by` varchar(50) NOT NULL DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_invoice_number` (`invoice_number`),
  KEY `idx_client_id` (`client_id`),
  CONSTRAINT `fk_invoices_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `invoices` (`id`, `invoice_number`, `client_id`, `issue_date`, `due_date`, `status`, `payment_method`, `payment_term`, `installment_months`, `monthly_payment`, `partner_assigned`, `ibraheem_share`, `fawas_share`, `subtotal`, `discount`, `tax_amount`, `grand_total`, `paid_amount`, `remaining_balance`, `price_note`, `amount_due_note`, `package_includes`, `notes`, `submitted_by`, `created_at`) VALUES
(1, '00364', 1, '2026-09-30', '2026-10-30', 'partial', 'gcash', 'custom', 12, 7500.00, 'both', 7500.00, 7500.00, 90000.00, 0.00, 0.00, 90000.00, 15000.00, 37500.00, 'Php 90,000 Per Year Or PHP 7,500 Per Month', '15,000 php partial payment (5th payment for monthly php 7,500)', '[\"1. Web design / Web system\",\"2. Job post / Job Offer\",\"3 Deployment System\",\"4. Company E-mail Server Maintenance\",\"5. Location Rescue system\",\"6. E-commerce site with SSL Security\",\"7. PRA Deployment\",\"8. FRA/SRA SYSTEM (IF NEEDED)\",\"9. Block Listing for OFW\",\"10. Voucher System\",\"Hosting and domain, with SSL security\",\"--- Nothing Follows ---\"]', 'We are open for your suggestions on improving our services with you.\nCan the payment be transferred through STC Pay or Gcash and Gcash # + 09617190588\nThanking you for doing Business with us!', 'admin', CURRENT_TIMESTAMP),
(2, '00372', 2, '2026-10-30', '2026-11-15', 'paid', 'gcash', 'full', 1, 7500.00, 'both', 3750.00, 3750.00, 7500.00, 0.00, 0.00, 7500.00, 7500.00, 0.00, 'PHP 7,500 Per Month', '7,500 PHP', '[\"1. Web design / Web system\",\"2. Job post / Job Offer\",\"3 Deployment System (QMS)\",\"4. Maintenance System\",\"5. Location Rescue system\",\"6. E-commerce site with SSL Security\",\"7. PRA Deployment\",\"8. FRA/SRA SYSTEM (IF NEEDED)\",\"9. Block Listing for OFW\",\"10. Voucher System\",\"Hosting and domain, with SSL security\",\"--- Nothing Follows ---\"]', 'We are open for your suggestions on improving our services with you.\nCan the payment be transferred through STC Pay or Gcash and Gcash # 09617190588\nThanking you for doing Business with us!', 'admin', CURRENT_TIMESTAMP),
(3, '00355', 3, '2026-10-26', '2026-11-10', 'paid', 'gcash', 'full', 1, 5000.00, 'ibraheem', 5000.00, 0.00, 5000.00, 0.00, 0.00, 5000.00, 5000.00, 0.00, 'Php 5,000', 'Php 5,000', '[\"Web and System (Online) & Maintenance Services for the Month of Oct\"]', 'We are open for your suggestions on improving our services with you.\nThank you for doing Business with us!', 'admin', CURRENT_TIMESTAMP),
(4, '0369', 4, '2026-10-30', '2026-11-15', 'paid', 'gcash', 'full', 1, 5500.00, 'both', 2750.00, 2750.00, 5500.00, 0.00, 0.00, 5500.00, 5500.00, 0.00, 'Maintenance', 'Maintenance', '[\"1. Web design / Web system\",\"2. Job post / Job Offer\",\"3. Technical Support (Hardware Maintenance)\",\"4. Company E-mail Server Maintenance\",\"5. Location Rescue system\",\"6. E-commerce site with SSL Security\",\"7. PRA Deployment\",\"8. FRA/SRA SYSTEM (IF NEEDED)\",\"9. Company Email Maintenance\",\"10. Voucher System\",\"Hosting and domain, with SSL security\",\"--- Nothing Follows ---\"]', 'We are open for your suggestions on improving our services with you.\nCan the payment be transferred through Gcash 09617190588\nThanking you for doing Business with us!', 'admin', CURRENT_TIMESTAMP)
ON DUPLICATE KEY UPDATE `grand_total`=VALUES(`grand_total`), `paid_amount`=VALUES(`paid_amount`), `remaining_balance`=VALUES(`remaining_balance`), `partner_assigned`=VALUES(`partner_assigned`), `ibraheem_share`=VALUES(`ibraheem_share`), `fawas_share`=VALUES(`fawas_share`);

-- ----------------------------
-- Table structure for invoice_items
-- ----------------------------
CREATE TABLE IF NOT EXISTS `invoice_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT 1.00,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `idx_invoice_id` (`invoice_id`),
  CONSTRAINT `fk_items_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_name`, `description`, `quantity`, `unit_price`, `discount`, `tax_rate`, `total`) VALUES
(1, 1, 'Package of E-Commerce Maintenance Services and Web maintenance including Rescue System', 'Includes: 1. Web design / Web system\n2. Job post / Job Offer\n3 Deployment System\n4. Company E-mail Server Maintenance\n5. Location Rescue system\n6. E-commerce site with SSL Security\n7. PRA Deployment\n8. FRA/SRA SYSTEM (IF NEEDED)\n9. Block Listing for OFW\n10. Voucher System\nHosting and domain, with SSL security\n--- Nothing Follows ---', 1.00, 90000.00, 0.00, 0.00, 90000.00),
(2, 2, 'Package of E-Commerce Maintenance Services and Web maintenance including Rescue System', 'Includes: 1. Web design / Web system\n2. Job post / Job Offer\n3 Deployment System (QMS)\n4. Maintenance System\n5. Location Rescue system\n6. E-commerce site with SSL Security\n7. PRA Deployment\n8. FRA/SRA SYSTEM (IF NEEDED)\n9. Block Listing for OFW\n10. Voucher System\nHosting and domain, with SSL security\n--- Nothing Follows ---', 1.00, 7500.00, 0.00, 0.00, 7500.00),
(3, 3, 'Web and System (Online) & Maintenance Services for the Month of Oct', 'Web and System (Online) & Maintenance Services for the Month of Oct', 1.00, 5000.00, 0.00, 0.00, 5000.00),
(4, 4, 'Package of E-Commerce Maintenance Services and Web maintenance including Rescue System', 'Includes: 1. Web design / Web system\n2. Job post / Job Offer\n3. Technical Support (Hardware Maintenance)\n4. Company E-mail Server Maintenance\n5. Location Rescue system\n6. E-commerce site with SSL Security\n7. PRA Deployment\n8. FRA/SRA SYSTEM (IF NEEDED)\n9. Company Email Maintenance\n10. Voucher System\nHosting and domain, with SSL security\n--- Nothing Follows ---', 1.00, 5500.00, 0.00, 0.00, 5500.00)
ON DUPLICATE KEY UPDATE `total`=VALUES(`total`);

-- ----------------------------
-- Table structure for login_attempts (Brute force protection)
-- ----------------------------
CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `attempt_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(20) NOT NULL DEFAULT 'failed',
  PRIMARY KEY (`id`),
  KEY `idx_ip_time` (`ip_address`, `attempt_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for security_logs (Audit trail & incident logs)
-- ----------------------------
CREATE TABLE IF NOT EXISTS `security_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_type` varchar(50) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_event_type` (`event_type`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for rate_limits (Anti-spam rate limiter)
-- ----------------------------
CREATE TABLE IF NOT EXISTS `rate_limits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `endpoint` varchar(100) NOT NULL,
  `request_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_rate_limit` (`ip_address`, `endpoint`, `request_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
