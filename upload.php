<?php
require_once 'functions.php';
require_login(); // must be logged in to upload

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $subject     = trim($_POST['subject'] ?? '');
    $file_type   = trim($_POST['file_type'] ?? '');
    $author      = trim($_POST['author'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($title === '')   $errors[] = "Title is required.";
    if ($subject === '') $errors[] = "Subject is required.";

    if (empty($errors)) {
        $upload = upload_resource_file($_FILES['file'] ?? []);
        if (!$upload['success']) {
            $errors[] = $upload['message'];
        } else {
            // If file type wasn't typed in, infer it from the extension
            if ($file_type === '') {
                $file_type = strtolower(pathinfo($upload['path'], PATHINFO_EXTENSION));
            }

            $t  = sanitize($conn, $title);
            $s  = sanitize($conn, $subject);
            $ft = sanitize($conn, $file_type);
            $a  = sanitize($conn, $author);
            $d  = sanitize($conn, $description);
            $fp = sanitize($conn, $upload['path']);
            $uid = (int)$_SESSION['user_id'];

            $sql = "INSERT INTO resources (title, subject, file_type, author, description, file_path, uploaded_by)
                    VALUES ('$t', '$s', '$ft', '$a', '$d', '$fp', $uid)";

            if (mysqli_query($conn, $sql)) {
                $success = true;
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
  <title>DOCSHARE - Share and Review Your Academic Contributions</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="upload.css">
</head>
<body>

  <?php $active = 'upload'; include 'nav.php'; ?>

  <main class="page-container">
    <div class="split-layout">

      <section class="hero-section">
        <h1 class="hero-title">
          <span class="underline">Share and</span><br>
          <span class="underline">Review</span><br>
          <span class="underline">Your</span><br>
          <span class="underline">Academic</span><br>
          <span class="underline">Contributions</span>
        </h1>
      </section>

      <section class="upload-section">

        <?php if ($success): ?>
          <p style="background:#d4edda;color:#155724;padding:12px 16px;border-radius:8px;margin-bottom:1rem;font-weight:600;">
            Resource uploaded successfully!
          </p>
        <?php endif; ?>

        <?php foreach ($errors as $err): ?>
          <p style="background:#f8d7da;color:#721c24;padding:10px 16px;border-radius:8px;margin-bottom:0.6rem;font-weight:600;">
            <?php echo htmlspecialchars($err); ?>
          </p>
        <?php endforeach; ?>

        <form class="upload-form" method="POST" action="upload.php" enctype="multipart/form-data">

          <div class="drop-zone" id="drop-zone">
            <i class="fa-solid fa-cloud-arrow-up drop-icon"></i>
            <span class="drop-title" id="drop-label">Drag and Drop</span>
            <span class="drop-subtitle">Your File</span>
            <input type="file" id="file-input" name="file" hidden required>
          </div>

          <div class="form-fields">

            <div class="form-group">
              <label for="title">Title</label>
              <input type="text" id="title" name="title" placeholder="Resource Title" required>
            </div>

            <div class="form-group">
              <label for="subject">Subject</label>
              <input type="text" id="subject" name="subject" placeholder="Subject Name or Subject code" required>
            </div>

            <div class="form-group">
              <label for="file-type">File Type</label>
              <input type="text" id="file-type" name="file_type" placeholder="e.g. pdf, docx (auto-detected if left blank)">
            </div>

            <div class="form-group">
              <label for="author">Author</label>
              <input type="text" id="author" name="author" placeholder="Author" value="<?php echo htmlspecialchars(current_user_name()); ?>">
            </div>

            <div class="form-group">
              <label for="description">Description</label>
              <input type="text" id="description" name="description" placeholder="Resource Description">
            </div>

            <button type="submit" class="btn-submit-upload" style="background:black;color:#fff;border:none;padding:10px 20px;border-radius:20px;font-weight:700;cursor:pointer;">
              Upload
            </button>

          </div>

        </form>
      </section>

    </div>
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
        <a href="#" aria-label="X"><i class="fa-brands fa-x-twitter"></i></a>
        <a href="#" aria-label="Website"><i class="fa-solid fa-globe"></i></a>
        <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
      </div>
    </div>
  </footer>

  <script>
    // Show the chosen file name and allow clicking the drop-zone to open the file picker
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('file-input');
    const dropLabel = document.getElementById('drop-label');

    dropZone.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', () => {
      if (fileInput.files.length > 0) {
        dropLabel.textContent = fileInput.files[0].name;
      }
    });
    dropZone.addEventListener('dragover', (e) => e.preventDefault());
    dropZone.addEventListener('drop', (e) => {
      e.preventDefault();
      if (e.dataTransfer.files.length > 0) {
        fileInput.files = e.dataTransfer.files;
        dropLabel.textContent = e.dataTransfer.files[0].name;
      }
    });
  </script>
</body>
</html>
