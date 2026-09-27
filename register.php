<?php
require_once 'functions.php';

if (is_logged_in()) {
    header("Location: index.php");
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $errors[] = "All fields are required.";
    }
    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if (empty($errors)) {
        $safe_email = sanitize($conn, $email);
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$safe_email' LIMIT 1");
        if ($check && mysqli_num_rows($check) > 0) {
            $errors[] = "An account with that email already exists.";
        } else {
            $safe_name = sanitize($conn, $name);
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (name, email, password) VALUES ('$safe_name', '$safe_email', '$hashed')";
            if (mysqli_query($conn, $sql)) {
                header("Location: index.php?registered=1");
                exit();
            } else {
                $errors[] = "Database error: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DOCSHARE - Register</title>
  <link rel="stylesheet" href="login.css">
</head>
<body>

  <?php $active = ''; include 'nav.php'; ?>

  <main class="auth-container">
    <div class="auth-card">
      <h1 class="auth-title">Create Account</h1>
      <p class="auth-subtitle">Join DOCSHARE to upload and share resources.</p>

      <?php foreach ($errors as $err): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($err); ?></div>
      <?php endforeach; ?>

      <form method="POST" action="register.php">
        <div class="form-group">
          <label for="name">Full Name</label>
          <input type="text" id="name" name="name" placeholder="Your name" required>
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="you@example.com" required>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="At least 6 characters" required>
        </div>
        <div class="form-group">
          <label for="confirm_password">Confirm Password</label>
          <input type="password" id="confirm_password" name="confirm_password" placeholder="Repeat password" required>
        </div>
        <button type="submit" class="btn-submit">Register</button>
      </form>

      <p class="auth-switch">Already have an account? <a href="login.php">Login here</a></p>
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
