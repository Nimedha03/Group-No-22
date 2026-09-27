<?php
require_once 'functions.php';
require_login(); // must be logged in to bookmark

$id       = (int)($_GET['id'] ?? 0);
$redirect = $_GET['redirect'] ?? 'browse.php';

if ($id > 0) {
    ds_toggle_bookmark($conn, $_SESSION['user_id'], $id);
}

// Only ever redirect back into this same app, never to an external URL
if (strpos($redirect, '://') !== false || strpos($redirect, '//') === 0 || strpos($redirect, "\n") !== false) {
    $redirect = 'browse.php';
}

header("Location: " . $redirect);
exit();
