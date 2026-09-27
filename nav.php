<?php
// Expects $active to be set by the including page: 'home' | 'browse' | 'upload' | 'features' | 'bookmarks' | 'reminders' | 'about' | 'contact'
$active = $active ?? '';
$nav_due_reminders = (function_exists('is_logged_in') && is_logged_in() && function_exists('ds_count_due_reminders') && isset($conn))
    ? ds_count_due_reminders($conn, $_SESSION['user_id'])
    : 0;
?>
<style>
  .nav-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 16px;
    height: 16px;
    padding: 0 4px;
    margin-left: 4px;
    border-radius: 999px;
    background-color: #C0392B;
    color: #fff;
    font-size: 0.65rem;
    font-weight: 700;
    vertical-align: middle;
  }
</style>
<header class="navbar">
  <div class="logo">
    <img src="image-removebg-preview.png" alt="DOCSHARE Logo" class="logo-image" width="200px">
  </div>
  <nav class="nav-links">
    <a href="index.php"   class="<?php echo $active === 'home'    ? 'active' : ''; ?>">Home</a>
    <?php if (is_logged_in()): ?>
      <a href="browse.php"  class="<?php echo $active === 'browse'  ? 'active' : ''; ?>">Browse</a>
      <a href="upload.php"  class="<?php echo $active === 'upload'  ? 'active' : ''; ?>">Upload</a>
      <a href="features.php" class="<?php echo in_array($active, ['features', 'bookmarks', 'reminders']) ? 'active' : ''; ?>">
        Features<?php if ($nav_due_reminders > 0): ?><span class="nav-badge"><?php echo $nav_due_reminders; ?></span><?php endif; ?>
      </a>
      <a href="about.php"   class="<?php echo $active === 'about'   ? 'active' : ''; ?>">About</a>
      <a href="contact.php" class="<?php echo $active === 'contact' ? 'active' : ''; ?>">Contact</a>
    <?php endif; ?>
  </nav>
  <?php if (is_logged_in()): ?>
    <a href="logout.php" class="btn-login" style="text-decoration:none;display:inline-flex;align-items:center;">
      Logout (<?php echo htmlspecialchars(current_user_name()); ?>)
    </a>
  <?php else: ?>
    <a href="login.php" class="btn-login" style="text-decoration:none;display:inline-flex;align-items:center;">Login</a>
  <?php endif; ?>
</header>