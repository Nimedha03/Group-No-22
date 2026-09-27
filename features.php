<?php require_once 'functions.php'; require_login(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DOCSHARE - Features</title>
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="stylesheet" href="browse.css">
  <link rel="stylesheet" href="extra.css">
</head>
<body>

  <?php $active = 'features'; include 'nav.php'; ?>

  <main class="container">
    <h1 class="page-title">Features</h1>
    <p style="color:#555;margin-top:-1.2rem;margin-bottom:1.5rem;max-width:640px;">
      Beyond browsing and uploading — save resources for later, search deeper, and get reminded when it's time to come back to something.
    </p>

    <div class="hub-grid">

      <div class="hub-card">
        <i data-lucide="bookmark" class="hub-icon"></i>
        <h3>Bookmarks</h3>
        <p>Save any resource with one click and find everything you've saved in one place.</p>
        <?php if (is_logged_in()): ?>
          <a href="bookmarks.php" class="btn-apply" style="text-decoration:none;">My Bookmarks</a>
        <?php else: ?>
          <a href="login.php?redirect=bookmarks" class="btn-apply" style="text-decoration:none;">Login to Bookmark</a>
        <?php endif; ?>
      </div>

      <div class="hub-card">
        <i data-lucide="search" class="hub-icon"></i>
        <h3>Advanced Search</h3>
        <p>Go past subject and file type — filter by author name and by the date a resource was uploaded.</p>
        <a href="browse.php#advanced-search" class="btn-apply" style="text-decoration:none;">Open Advanced Search</a>
      </div>

      <div class="hub-card">
        <i data-lucide="bell" class="hub-icon"></i>
        <h3>
          Reminders
          <?php if (is_logged_in()):
            $due = ds_count_due_reminders($conn, $_SESSION['user_id']);
            if ($due > 0): ?>
              <span class="badge"><?php echo $due; ?></span>
          <?php endif; endif; ?>
        </h3>
        <p>Set yourself a reminder — optionally tied to a resource — and see it flagged as due right in the nav bar.</p>
        <?php if (is_logged_in()): ?>
          <a href="reminders.php" class="btn-apply" style="text-decoration:none;">My Reminders</a>
        <?php else: ?>
          <a href="login.php?redirect=reminders" class="btn-apply" style="text-decoration:none;">Login to Set Reminders</a>
        <?php endif; ?>
      </div>

    </div>
  </main>

  <script>
    lucide.createIcons();
  </script>
</body>
</html>
