<?php
require_once 'functions.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: browse.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM resources WHERE id = $id");
$resource = $result ? mysqli_fetch_assoc($result) : null;

if (!$resource) {
    header("Location: browse.php");
    exit();
}


mysqli_query($conn, "UPDATE resources SET downloads = downloads + 1 WHERE id = $id");

$file_path = __DIR__ . '/' . $resource['file_path'];

if (file_exists($file_path)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
    header('Content-Length: ' . filesize($file_path));
    readfile($file_path);
    exit();
} else {
    
    header("Location: browse.php?error=missing_file");
    exit();
}
?>
