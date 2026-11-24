<?php
/**
 * Local Hosting Database Override (for InfinityFree, cPanel, Hostinger, etc.)
 *
 * Instructions:
 * 1. Copy/rename this file to `config.local.php` on your web host.
 * 2. Fill in the MySQL details provided by your hosting control panel.
 * 3. `config.local.php` is gitignored so your credentials will never be pushed to GitHub.
 */

define('DB_DRIVER', 'mysql'); // Use 'mysql' for shared hosting MySQL, or 'sqlite' for file-based DB
define('DB_HOST', 'sqlXXX.infinityfree.com'); // e.g. localhost or sqlXXX.epizy.com / sqlXXX.infinityfree.com
define('DB_PORT', '3306');
define('DB_NAME', 'if0_XXXXXXXX_yaramay');    // Your hosting database name
define('DB_USER', 'if0_XXXXXXXX');            // Your hosting database username
define('DB_PASS', 'YOUR_DATABASE_PASSWORD');  // Your hosting database password
