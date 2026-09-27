<?php
require_once 'functions.php';
require_login();

$search    = $_GET['q'] ?? '';
$subject   = $_GET['subject'] ?? '';
$file_type = $_GET['file_type'] ?? '';
$sort      = $_GET['sort'] ?? 'latest';
$author    = $_GET['author'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to   = $_GET['date_to'] ?? '';
$page      = max(1, (int)($_GET['page'] ?? 1));
$per_page  = 6;
$offset    = ($page - 1) * $per_page;

$total_results = ds_count_resources($conn, $search, $subject, $file_type, $author, $date_from, $date_to);
$total_pages   = max(1, ceil($total_results / $per_page));
$page          = min($page, $total_pages);
$offset        = ($page - 1) * $per_page;

$resources = ds_get_resources($conn, $search, $subject, $file_type, $sort, $per_page, $offset, $author, $date_from, $date_to);
$all_subjects = ds_get_all_subjects($conn);
$bookmarked_ids = is_logged_in() ? ds_get_bookmarked_ids($conn, $_SESSION['user_id']) : [];

function qs($overrides = []) {
    $params = array_merge($_GET, $overrides);
    return htmlspecialchars('browse.php?' . http_build_query($params));
}

// Unescaped version (used only inside a redirect= query param, never printed raw into HTML text)
function raw_qs($overrides = []) {
    $params = array_merge($_GET, $overrides);
    return 'browse.php?' . http_build_query($params);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DOCSHARE - Browse Academic Resources</title>
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="stylesheet" href="browse.css">
</head>
<body>

  <?php $active = 'browse'; include 'nav.php'; ?>

  <main class="container">
    <h1 class="page-title">Browse Academic Resources</h1>

    <div class="content-wrapper">

      <aside class="sidebar">
        <h3>Filters</h3>
        <form method="GET" action="browse.php">
          <div class="filter-group">
            <label for="search">Search</label>
            <input type="text" id="search" name="q" placeholder="Search notes..." value="<?php echo htmlspecialchars($search); ?>">
          </div>

          <div class="filter-group">
            <label for="subject">Subject</label>
            <select id="subject" name="subject">
              <option value="">All Subjects</option>
              <?php foreach ($all_subjects as $subj): ?>
                <option value="<?php echo htmlspecialchars($subj); ?>" <?php echo $subject === $subj ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($subj); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="filter-group">
            <label for="file-type">File Type</label>
            <select id="file-type" name="file_type">
              <option value="">All Types</option>
              <option value="pdf"  <?php echo $file_type === 'pdf'  ? 'selected' : ''; ?>>PDF</option>
              <option value="docx" <?php echo $file_type === 'docx' ? 'selected' : ''; ?>>DOCX</option>
              <option value="pptx" <?php echo $file_type === 'pptx' ? 'selected' : ''; ?>>PPTX</option>
              <option value="zip"  <?php echo $file_type === 'zip'  ? 'selected' : ''; ?>>ZIP</option>
            </select>
          </div>

          <div class="filter-group">
            <label for="sort-by">Sort By</label>
            <select id="sort-by" name="sort">
              <option value="latest"  <?php echo $sort === 'latest'  ? 'selected' : ''; ?>>Latest</option>
              <option value="popular" <?php echo $sort === 'popular' ? 'selected' : ''; ?>>Most Popular</option>
            </select>
          </div>

          <details id="advanced-search" class="adv-search" <?php echo ($author !== '' || $date_from !== '' || $date_to !== '') ? 'open' : ''; ?>>
            <summary>Advanced Search</summary>
            <div class="filter-group">
              <label for="author">Author</label>
              <input type="text" id="author" name="author" placeholder="Author name" value="<?php echo htmlspecialchars($author); ?>">
            </div>
            <div class="filter-group">
              <label for="date_from">Uploaded From</label>
              <input type="date" id="date_from" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>">
            </div>
            <div class="filter-group">
              <label for="date_to">Uploaded To</label>
              <input type="date" id="date_to" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>">
            </div>
          </details>

          <button type="submit" class="btn-apply">Apply Filters</button>
        </form>
      </aside>

      <section class="results-section">
        <h2 class="results-count">Showing <?php echo $total_results; ?> Result<?php echo $total_results === 1 ? '' : 's'; ?></h2>

        <div class="card-list">
          <?php if (empty($resources)): ?>
            <p style="color:#555;">No resources match your filters yet.</p>
          <?php endif; ?>

          <?php foreach ($resources as $res): $is_bm = isset($bookmarked_ids[(int)$res['id']]); ?>
            <div class="card">
              <div class="card-info">
                <i data-lucide="<?php echo ds_icon_for_type($res['file_type']); ?>" class="card-icon"></i>
                <span class="card-title"><?php echo htmlspecialchars($res['title']); ?></span>
              </div>
              <div class="card-actions">
                <?php if (is_logged_in()): ?>
                  <a href="bookmark_toggle.php?id=<?php echo (int)$res['id']; ?>&redirect=<?php echo urlencode(raw_qs()); ?>"
                     class="btn-bookmark <?php echo $is_bm ? 'active' : ''; ?>"
                     aria-label="<?php echo $is_bm ? 'Remove bookmark' : 'Add bookmark'; ?>">
                    <i data-lucide="<?php echo $is_bm ? 'bookmark-check' : 'bookmark'; ?>"></i>
                  </a>
                <?php else: ?>
                  <a href="login.php" class="btn-bookmark" aria-label="Login to bookmark">
                    <i data-lucide="bookmark"></i>
                  </a>
                <?php endif; ?>
                <a href="download.php?id=<?php echo (int)$res['id']; ?>" class="btn-download" aria-label="Download">
                  <i data-lucide="download"></i>
                </a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <?php if ($total_pages > 1): ?>
        <div class="pagination">
          <a href="<?php echo qs(['page' => max(1, $page - 1)]); ?>" class="page-btn page-nav" aria-label="Previous Page">&lt;</a>
          <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="<?php echo qs(['page' => $i]); ?>" class="page-btn <?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
          <?php endfor; ?>
          <a href="<?php echo qs(['page' => min($total_pages, $page + 1)]); ?>" class="page-btn page-nav" aria-label="Next Page">&gt;</a>
        </div>
        <?php endif; ?>

      </section>

    </div>
  </main>

  <script>
    lucide.createIcons();
  </script>
</body>
</html>