<?php
include("conn.php");
session_start();

// Only educators can access


// Handle form submission
if (isset($_POST['submit'])) {
    $title = mysqli_real_escape_string($dbConn, $_POST['title']);
    $description = mysqli_real_escape_string($dbConn, $_POST['description']);

    $insertQuery = "INSERT INTO modules (ModuleTitle, ModuleDescription, CreatedBy, CreatedDate) 
                    VALUES ('$title', '$description', '$educatorUsername', CURDATE())";

    if (mysqli_query($dbConn, $insertQuery)) {
        echo "<script>alert('Module uploaded successfully!'); window.location='upload_module.php';</script>";
        exit;
    } else {
        echo "<script>alert('Upload failed.'); window.history.back();</script>";
        exit;
    }
}

// Fetch all modules
$moduleQuery = "SELECT * FROM modules ORDER BY CreatedDate DESC";
$moduleResult = mysqli_query($dbConn, $moduleQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Upload Module</title>
<link rel="stylesheet" href="upload_module.css">
</head>
<body >

<div class="main">

    <!-- Back Button -->
    <a href="Educator_Dashboard.php" class="back-btn">← Back to Dashboard</a>

    <h1>Upload New Module</h1>

    <!-- Upload Module Form -->
    <form action="upload_module.php" method="post" class="module-form">
        <label>Module Title:</label>
        <input type="text" name="title" required><br>

        <label>Module Description:</label><br>
        <textarea name="description" rows="5" required></textarea><br>

        <button type="submit" name="submit" class="btn">Upload Module</button>
    </form>

    <hr>

    <!-- Modules List -->
    <h2>Your Uploaded Modules</h2>
    <div class="module-grid">
        <?php if(mysqli_num_rows($moduleResult) > 0): ?>
            <?php while ($module = mysqli_fetch_assoc($moduleResult)): ?>
                <div class="module-card">
                    <h3><?= htmlspecialchars($module['ModuleTitle']) ?></h3>
                    <p><?= nl2br(htmlspecialchars($module['ModuleDescription'])) ?></p>
                    <small>Created by <?= htmlspecialchars($module['CreatedBy']) ?> on <?= $module['CreatedDate'] ?></small>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No modules uploaded yet.</p>
        <?php endif; ?>
    </div>

</div>

<!-- Optional JavaScript -->
<script>
    // Example: confirm before submitting (optional)
    const form = document.querySelector('.module-form');
    form.addEventListener('submit', function(e) {
        if(!confirm('Are you sure you want to upload this module?')) {
            e.preventDefault();
        }
    });
</script>

</body>
</html>
