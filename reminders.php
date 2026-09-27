<?php
require_once 'functions.php';
require_login();

$user_id = $_SESSION['user_id'];
$errors  = [];
$success = false;

// Add a new reminder
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $title       = trim($_POST['title'] ?? '');
    $note        = trim($_POST['note'] ?? '');
    $remind_at   = trim($_POST['remind_at'] ?? ''); // comes in as 'Y-m-dTH:i' from datetime-local
    $resource_id = $_POST['resource_id'] ?? '';

    if ($title === '')     $errors[] = "Title is required.";
    if ($remind_at === '') $errors[] = "Reminder date/time is required.";

    if (empty($errors)) {
        $resource_id   = $resource_id !== '' ? (int) $resource_id : null;
        $remind_at_sql = str_replace('T', ' ', $remind_at) . ':00';

        if (ds_add_reminder($conn, $user_id, $resource_id, $title, $note, $remind_at_sql)) {
            $success = true;
        } else {
            $errors[] = "Database error: " . mysqli_error($conn);
        }
    }
}

// Toggle done / delete via simple GET links (consistent with download.php's style in this project)
if (isset($_GET['toggle'])) {
    ds_toggle_reminder_done($conn, $user_id, (int) $_GET['toggle']);
    header("Location: reminders.php");
    exit();
}
if (isset($_GET['delete'])) {
    ds_delete_reminder($conn, $user_id, (int) $_GET['delete']);
    header("Location: reminders.php");
    exit();
}

$reminders        = ds_get_reminders($conn, $user_id);
$resource_options = ds_get_resources($conn, '', '', '', 'latest', 200, 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DOCSHARE - My Reminders</title>
  <link rel="stylesheet" href="browse.css">
  <link rel="stylesheet" href="extra.css">
</head>
<body>

  <?php $active = 'reminders'; include 'nav.php'; ?>

  <main class="container">
    <h1 class="page-title">My Reminders</h1>

    <?php if ($success): ?>
      <div class="alert alert-success">Reminder added.</div>
    <?php endif; ?>
    <?php foreach ($errors as $err): ?>
      <div class="alert alert-error"><?php echo htmlspecialchars($err); ?></div>
    <?php endforeach; ?>

    <form method="POST" action="reminders.php" class="reminder-form">
      <input type="hidden" name="action" value="add">

      <div class="filter-group">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" placeholder="e.g. Review Chapter 2 notes" required>
      </div>

      <div class="filter-group">
        <label for="remind_at">Remind me at</label>
        <input type="datetime-local" id="remind_at" name="remind_at" required>
      </div>

      <div class="filter-group">
        <label for="resource_id">Link to a resource (optional)</label>
        <select id="resource_id" name="resource_id">
          <option value="">— None —</option>
          <?php foreach ($resource_options as $r): ?>
            <option value="<?php echo (int) $r['id']; ?>"><?php echo htmlspecialchars($r['title']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="filter-group">
        <label for="note">Note (optional)</label>
        <input type="text" id="note" name="note" placeholder="Any details...">
      </div>

      <button type="submit" class="btn-apply">Add Reminder</button>
    </form>

    <div class="reminder-list">
      <?php if (empty($reminders)): ?>
        <p style="color:#555;">No reminders yet — add one above.</p>
      <?php endif; ?>

      <?php foreach ($reminders as $r):
        $is_overdue = !$r['is_done'] && strtotime($r['remind_at']) <= time();
        $state = $r['is_done'] ? 'done' : ($is_overdue ? 'overdue' : '');
      ?>
        <div class="reminder-item <?php echo $state; ?>">
          <div class="reminder-main">
            <span class="reminder-title"><?php echo htmlspecialchars($r['title']); ?></span>
            <span class="reminder-time">
              <?php echo date('M j, Y g:i A', strtotime($r['remind_at'])); ?>
              <?php if ($is_overdue): ?> · due<?php endif; ?>
            </span>
            <?php if (!empty($r['resource_title'])): ?>
              <span class="reminder-linked">Linked: <?php echo htmlspecialchars($r['resource_title']); ?></span>
            <?php endif; ?>
            <?php if (!empty($r['note'])): ?>
              <p class="reminder-note"><?php echo htmlspecialchars($r['note']); ?></p>
            <?php endif; ?>
          </div>
          <div class="reminder-actions">
            <a href="reminders.php?toggle=<?php echo (int) $r['id']; ?>" class="btn-small">
              <?php echo $r['is_done'] ? 'Undo' : 'Done'; ?>
            </a>
            <a href="reminders.php?delete=<?php echo (int) $r['id']; ?>" class="btn-small btn-danger"
               onclick="return confirm('Delete this reminder?');">Delete</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </main>

</body>
</html>
