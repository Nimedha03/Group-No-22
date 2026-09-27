<?php
require_once 'functions.php';
require_login();

$bookmarks = ds_get_bookmarked_resources($conn, $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DOCSHARE - My Bookmarks</title>
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="stylesheet" href="browse.css">
  <link rel="stylesheet" href="extra.css">
</head>
<body>

  <?php $active = 'bookmarks'; include 'nav.php'; ?>

  <main class="container">
    <h1 class="page-title">My Bookmarks</h1>

    <div class="content-wrapper" style="grid-template-columns: 1fr;">
      <section class="results-section">
        <h2 class="results-count">
          <?php echo count($bookmarks); ?> Bookmark<?php echo count($bookmarks) === 1 ? '' : 's'; ?>
        </h2>

        <div class="card-list">
          <?php if (empty($bookmarks)): ?>
            <div class="empty-state">
              <p>No bookmarks yet — save a resource from Browse and it'll show up here.</p>
              <a href="browse.php" class="btn-apply" style="text-decoration:none;display:inline-block;">Browse Resources</a>
            </div>
          <?php endif; ?>

          <?php foreach ($bookmarks as $res): ?>
            <div class="card">
              <div class="card-info">
                <i data-lucide="<?php echo ds_icon_for_type($res['file_type']); ?>" class="card-icon"></i>
                <span class="card-title"><?php echo htmlspecialchars($res['title']); ?></span>
              </div>
              <div class="card-actions">
                <a href="bookmark_toggle.php?id=<?php echo (int)$res['id']; ?>&redirect=<?php echo urlencode('bookmarks.php'); ?>"
                   class="btn-bookmark active" aria-label="Remove bookmark">
                  <i data-lucide="bookmark-check"></i>
                </a>
                <a href="download.php?id=<?php echo (int)$res['id']; ?>" class="btn-download" aria-label="Download">
                  <i data-lucide="download"></i>
                </a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    </div>
  </main>

  <script>
    lucide.createIcons();
  </script>
</body>
</html>
