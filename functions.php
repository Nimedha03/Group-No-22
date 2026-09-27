<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';

if (!function_exists('sanitize')) {
/** Escape user input before putting it in a query. */
function sanitize($conn, $data) {
    return mysqli_real_escape_string($conn, trim($data));
}
}

if (!function_exists('is_logged_in')) {
/** Is someone currently logged in? */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}
}

if (!function_exists('require_login')) {
/** Send anonymous visitors to the login page, remembering where they were headed. */
function require_login() {
    if (!is_logged_in()) {
        $_SESSION['login_redirect'] = $_SERVER['REQUEST_URI'] ?? 'index.php';
        header("Location: login.php");
        exit();
    }
}
}

if (!function_exists('current_user_name')) {
function current_user_name() {
    return $_SESSION['user_name'] ?? '';
}
}

if (!function_exists('upload_resource_file')) {
/**
 * Handle a single uploaded file: validate type/size and move it into /uploads.
 * Returns ['success' => bool, 'path' => string|null, 'message' => string|null]
 */
function upload_resource_file($file) {
    $target_dir = __DIR__ . "/uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Please choose a file to upload.'];
    }

    $allowed = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'zip', 'txt', 'xls', 'xlsx'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        return ['success' => false, 'message' => 'File type .' . $ext . ' is not allowed.'];
    }

    if ($file['size'] > 20 * 1024 * 1024) { // 20MB cap
        return ['success' => false, 'message' => 'File is too large (max 20MB).'];
    }

    $safe_name = uniqid('doc_', true) . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', basename($file['name']));
    $target_path = $target_dir . $safe_name;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return ['success' => true, 'path' => 'uploads/' . $safe_name];
    }
    return ['success' => false, 'message' => 'Upload failed. Check the uploads/ folder permissions.'];
}
}

if (!function_exists('ds_get_resources')) {
/** Fetch a filtered, sorted, paginated list of resources. $author/$date_from/$date_to power Advanced Search. */
function ds_get_resources($conn, $search = '', $subject = '', $file_type = '', $sort = 'latest', $limit = 10, $offset = 0, $author = '', $date_from = '', $date_to = '') {
    $search    = sanitize($conn, $search);
    $subject   = sanitize($conn, $subject);
    $file_type = sanitize($conn, $file_type);
    $author    = sanitize($conn, $author);
    $date_from = sanitize($conn, $date_from);
    $date_to   = sanitize($conn, $date_to);
    $limit     = (int) $limit;
    $offset    = (int) $offset;

    $sql = "SELECT * FROM resources WHERE 1=1";
    if ($search !== '')    $sql .= " AND title LIKE '%$search%'";
    if ($subject !== '')   $sql .= " AND subject = '$subject'";
    if ($file_type !== '') $sql .= " AND file_type = '$file_type'";
    if ($author !== '')    $sql .= " AND author LIKE '%$author%'";
    if ($date_from !== '') $sql .= " AND DATE(created_at) >= '$date_from'";
    if ($date_to !== '')   $sql .= " AND DATE(created_at) <= '$date_to'";
    $sql .= ($sort === 'popular') ? " ORDER BY downloads DESC" : " ORDER BY created_at DESC";
    $sql .= " LIMIT $limit OFFSET $offset";

    $result = mysqli_query($conn, $sql);
    $resources = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $resources[] = $row;
        }
    }
    return $resources;
}
}

if (!function_exists('ds_count_resources')) {
/** Count how many resources match the current filters (for pagination). Same Advanced Search filters as ds_get_resources. */
function ds_count_resources($conn, $search = '', $subject = '', $file_type = '', $author = '', $date_from = '', $date_to = '') {
    $search    = sanitize($conn, $search);
    $subject   = sanitize($conn, $subject);
    $file_type = sanitize($conn, $file_type);
    $author    = sanitize($conn, $author);
    $date_from = sanitize($conn, $date_from);
    $date_to   = sanitize($conn, $date_to);

    $sql = "SELECT COUNT(*) AS total FROM resources WHERE 1=1";
    if ($search !== '')    $sql .= " AND title LIKE '%$search%'";
    if ($subject !== '')   $sql .= " AND subject = '$subject'";
    if ($file_type !== '') $sql .= " AND file_type = '$file_type'";
    if ($author !== '')    $sql .= " AND author LIKE '%$author%'";
    if ($date_from !== '') $sql .= " AND DATE(created_at) >= '$date_from'";
    if ($date_to !== '')   $sql .= " AND DATE(created_at) <= '$date_to'";

    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return (int) $row['total'];
}
}

/* ---------------- Bookmarks ---------------- */

if (!function_exists('ds_toggle_bookmark')) {
/** Add or remove a bookmark for this user/resource pair. Returns true if now bookmarked, false if removed. */
function ds_toggle_bookmark($conn, $user_id, $resource_id) {
    $user_id     = (int) $user_id;
    $resource_id = (int) $resource_id;

    $check = mysqli_query($conn, "SELECT id FROM bookmarks WHERE user_id = $user_id AND resource_id = $resource_id LIMIT 1");
    if ($check && mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "DELETE FROM bookmarks WHERE user_id = $user_id AND resource_id = $resource_id");
        return false;
    }
    mysqli_query($conn, "INSERT INTO bookmarks (user_id, resource_id) VALUES ($user_id, $resource_id)");
    return true;
}
}

if (!function_exists('ds_get_bookmarked_ids')) {
/** resource_id => true map for the current user, so a resource list can flag which cards are bookmarked. */
function ds_get_bookmarked_ids($conn, $user_id) {
    $user_id = (int) $user_id;
    $ids = [];
    $result = mysqli_query($conn, "SELECT resource_id FROM bookmarks WHERE user_id = $user_id");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $ids[(int) $row['resource_id']] = true;
        }
    }
    return $ids;
}
}

if (!function_exists('ds_get_bookmarked_resources')) {
/** Full resource rows a user has bookmarked, most recently bookmarked first. */
function ds_get_bookmarked_resources($conn, $user_id) {
    $user_id = (int) $user_id;
    $sql = "SELECT r.*, b.created_at AS bookmarked_at
            FROM bookmarks b
            JOIN resources r ON r.id = b.resource_id
            WHERE b.user_id = $user_id
            ORDER BY b.created_at DESC";
    $result = mysqli_query($conn, $sql);
    $rows = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }
    return $rows;
}
}

/* ---------------- Reminders ---------------- */

if (!function_exists('ds_add_reminder')) {
/** $remind_at must already be a MySQL DATETIME string ('Y-m-d H:i:s'). */
function ds_add_reminder($conn, $user_id, $resource_id, $title, $note, $remind_at) {
    $user_id      = (int) $user_id;
    $resource_sql = $resource_id ? (int) $resource_id : 'NULL';
    $t = sanitize($conn, $title);
    $n = sanitize($conn, $note);
    $r = sanitize($conn, $remind_at);

    $sql = "INSERT INTO reminders (user_id, resource_id, title, note, remind_at) VALUES ($user_id, $resource_sql, '$t', '$n', '$r')";
    return mysqli_query($conn, $sql);
}
}

if (!function_exists('ds_get_reminders')) {
/** A user's reminders, unfinished-and-soonest first, with the linked resource title if any. */
function ds_get_reminders($conn, $user_id) {
    $user_id = (int) $user_id;
    $sql = "SELECT rem.*, res.title AS resource_title
            FROM reminders rem
            LEFT JOIN resources res ON res.id = rem.resource_id
            WHERE rem.user_id = $user_id
            ORDER BY rem.is_done ASC, rem.remind_at ASC";
    $result = mysqli_query($conn, $sql);
    $rows = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }
    return $rows;
}
}

if (!function_exists('ds_delete_reminder')) {
function ds_delete_reminder($conn, $user_id, $reminder_id) {
    $user_id     = (int) $user_id;
    $reminder_id = (int) $reminder_id;
    return mysqli_query($conn, "DELETE FROM reminders WHERE id = $reminder_id AND user_id = $user_id");
}
}

if (!function_exists('ds_toggle_reminder_done')) {
function ds_toggle_reminder_done($conn, $user_id, $reminder_id) {
    $user_id     = (int) $user_id;
    $reminder_id = (int) $reminder_id;
    return mysqli_query($conn, "UPDATE reminders SET is_done = 1 - is_done WHERE id = $reminder_id AND user_id = $user_id");
}
}

if (!function_exists('ds_count_due_reminders')) {
/** Not-yet-done reminders whose time has already arrived — drives the little nav badge. */
function ds_count_due_reminders($conn, $user_id) {
    $user_id = (int) $user_id;
    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM reminders WHERE user_id = $user_id AND is_done = 0 AND remind_at <= NOW()");
    $row = $result ? mysqli_fetch_assoc($result) : null;
    return $row ? (int) $row['total'] : 0;
}
}

if (!function_exists('ds_get_all_subjects')) {
/** Get every distinct subject currently in the resources table (for the filter dropdown). */
function ds_get_all_subjects($conn) {
    $subjects = [];
    $result = mysqli_query($conn, "SELECT DISTINCT subject FROM resources WHERE subject IS NOT NULL AND subject != '' ORDER BY subject");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $subjects[] = $row['subject'];
        }
    }
    return $subjects;
}
}

if (!function_exists('ds_icon_for_type')) {
/** Pick a Lucide icon name based on file extension. */
function ds_icon_for_type($file_type) {
    $map = [
        'pdf'  => 'file-text',
        'doc'  => 'file-text',
        'docx' => 'file-text',
        'ppt'  => 'presentation',
        'pptx' => 'presentation',
        'xls'  => 'sheet',
        'xlsx' => 'sheet',
        'zip'  => 'file-archive',
        'txt'  => 'file-text',
    ];
    $file_type = strtolower($file_type ?? '');
    return $map[$file_type] ?? 'file';
}
}
?>