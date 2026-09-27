<?php
require_once 'functions.php';
require_login();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $message === '') {
        $errors[] = "Name, email and message are required.";
    }
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    if (empty($errors)) {
        $n = sanitize($conn, $name);
        $e = sanitize($conn, $email);
        $s = sanitize($conn, $subject);
        $m = sanitize($conn, $message);

        $sql = "INSERT INTO reports (name, email, subject, message) VALUES ('$n', '$e', '$s', '$m')";

        if (mysqli_query($conn, $sql)) {
            $success = true;
        } else {
            $errors[] = "Database error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DOCSHARE - Contact / Report an Issue</title>
  <link rel="stylesheet" href="contact.css">
</head>
<body>

  <?php $active = 'contact'; include 'nav.php'; ?>

  <main class="contact-container">
    <h1 class="contact-title">Contact Us</h1>
    <p class="contact-subtitle">
      Found a broken link, a wrong upload, or just want to reach out? Send us a report below.
    </p>

    <div class="contact-card">

      <?php if ($success): ?>
        <div class="alert alert-success">Thanks — your report has been submitted.</div>
      <?php endif; ?>

      <?php foreach ($errors as $err): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($err); ?></div>
      <?php endforeach; ?>

      <form method="POST" action="contact.php">
        <div class="form-row">
          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" placeholder="Your name" required>
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="you@example.com" required>
          </div>
        </div>

        <div class="form-group">
          <label for="subject">Subject</label>
          <input type="text" id="subject" name="subject" placeholder="What's this about?">
        </div>

        <div class="form-group">
          <label for="message">Message / Report</label>
          <textarea id="message" name="message" placeholder="Describe the issue or your message..." required></textarea>
        </div>

        <button type="submit" class="btn-submit">Submit Report</button>
      </form>
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