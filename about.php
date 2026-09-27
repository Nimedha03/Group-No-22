<?php require_once 'functions.php'; require_login(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DOCSHARE - About</title>
  <link rel="stylesheet" href="about.css">
</head>
<body>

  <?php $active = 'about'; include 'nav.php'; ?>

  <main class="about-container">
    <h1 class="about-title">About DOCSHARE</h1>
    <p class="about-text">
      DOCSHARE is a central directory for BICT lecture notes, database guides, and exam
      archives. Instead of resources being scattered across group chats and personal drives,
      students can browse, search, and download everything from one place — and contribute
      their own notes back to the collection.
    </p>
    <p class="about-text">
      Built as a group project (Group No. 22), DOCSHARE runs on PHP and MySQL so any
      registered student can log in, upload a resource, and have it instantly searchable
      by subject, file type, and popularity.
    </p>

    <div class="about-cards">
      <div class="about-card">
        <h3>Browse</h3>
        <p>Search and filter resources by subject, file type, or how popular they are.</p>
      </div>
      <div class="about-card">
        <h3>Upload</h3>
        <p>Registered users can contribute lecture notes, guides, and past papers.</p>
      </div>
      <div class="about-card">
        <h3>Download</h3>
        <p>One click to grab any resource — download counts help surface the most useful ones.</p>
      </div>
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
