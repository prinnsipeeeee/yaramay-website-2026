<?php
require_once __DIR__ . '/config.php';

$_SESSION['admin_user'] = null;
unset($_SESSION['admin_user']);
if (session_status() === PHP_SESSION_ACTIVE) {
    session_regenerate_id(true);
}

header('Location: login.php');
exit;
