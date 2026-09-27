<?php require_once 'functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DOCSHARE - All Your ICT Resources</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="home.css">
</head>
<body>

  <?php $active = 'home'; include 'nav.php'; ?>

  <main class="hero-container">
    <section class="hero-content">
      <h1 class="hero-title">
        <span class="reveal-line delay-1">ALL YOUR</span><br>
        <span class="reveal-line delay-2"><span class="underline">ICT RESOURCES</span>.</span><br>
        <span class="reveal-line delay-3"><span class="underline">IN A SINGLE DIRECTORY</span></span>
      </h1>
      <p class="hero-subtitle reveal-fade delay-4">
        Streamlining BICT lecture assets, database guides, and exam archives into one powerful space.
      </p>

      <form class="search-box reveal-fade delay-5" action="browse.php" method="GET">
        <input type="text" name="q" placeholder="Search" aria-label="Search">
        <button type="submit" class="search-btn" aria-label="Submit search">
          <i class="fa-solid fa-magnifying-glass"></i>
        </button>
      </form>

      <?php if (isset($_GET['registered'])): ?>
        <div class="flash-msg flash-success">Account created! You can now log in.</div>
      <?php endif; ?>
    </section>
  </main>

  <footer class="footer">
    <div class="footer-logo">
      <i class="fa-regular fa-file-lines"></i>
      <span>DOCSHARE</span>
    </div>
    <div class="footer-copyright">
      &copy; 2026 DOCSHARE. All rights reserved
    </div>
    <div class="footer-socials">
      <span>Follow us on</span>
      <div class="social-icons">
        <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
        <a href="#" aria-label="X (Twitter)"><i class="fa-brands fa-x-twitter"></i></a>
        <a href="#" aria-label="Website"><i class="fa-solid fa-globe"></i></a>
        <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
      </div>
    </div>
  </footer>

</body>
</html>