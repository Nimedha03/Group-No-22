<?php
require_once 'functions.php';

if (is_logged_in()) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = "Please enter both email and password.";
    } else {
        $safe_email = sanitize($conn, $email);
        $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$safe_email' LIMIT 1");
        $user = $result ? mysqli_fetch_assoc($result) : null;

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            $redirect_map = [
                'upload'    => 'upload.php',
                'bookmarks' => 'bookmarks.php',
                'reminders' => 'reminders.php',
                'features'  => 'features.php',
            ];

            $target = 'index.php';
            if (!empty($_SESSION['login_redirect'])) {
                $target = $_SESSION['login_redirect'];
                unset($_SESSION['login_redirect']);
            } elseif (!empty($_GET['redirect'])) {
                $target = $redirect_map[$_GET['redirect']] ?? 'index.php';
            }
            // Safety: only ever redirect back inside this app
            if (strpos($target, '://') !== false || strpos($target, '//') === 0) {
                $target = 'index.php';
            }
            header("Location: " . $target);
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DOCSHARE - Login</title>
  <link rel="stylesheet" href="login.css">
</head>
<body>

  <?php $active = ''; include 'nav.php'; ?>

  <main class="auth-container">
    <div class="auth-card">
      <h1 class="auth-title">Welcome Back</h1>
      <p class="auth-subtitle">Log in to upload and manage your resources.</p>

      <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <form method="POST" action="login.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>">
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="you@example.com" required>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="********" required>
        </div>
        <button type="submit" class="btn-submit">Login</button>
      </form>

      <p class="auth-switch">Don't have an account? <a href="register.php">Register here</a></p>
    </div>
  </main>

  <footer class="footer">
    <div class="footer-logo"><span>DOCSHARE</span></div>
    <div class="footer-copyright">&copy; 2026 DOCSHARE. All rights reserved</div>
    <div class="footer-socials">
      <span>Follow us on</span>
      <div class="social-icons">
        <a href="#">FB</a><a href="#">X</a><a href="#">Web</a><a href="#">In</a>
      </div>
    </div>
  </footer>

</body>
</html>
