<?php
include("conn.php");
session_start();

/* Only Educators can access */
$role = $_SESSION['role'] = 'Educator';
$educatorUsername = $_SESSION['username'] ?? '';

if ($role !== "Educator") {
    echo "<script>alert('Only Educators can upload content.'); window.history.back();</script>";
    exit;
}

/* Handle form submission */
if (isset($_POST['submit'])) {

    $title = mysqli_real_escape_string($dbConn, $_POST['title']);
    $type = $_POST['type'];
    $description = mysqli_real_escape_string($dbConn, $_POST['description']);

    /* File upload (optional) */
    $filePath = '';
    if (!empty($_FILES['file']['name'])) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = time() . "_" . basename($_FILES['file']['name']);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
            $filePath = $targetFile;
        }
    }

    /* INSERT AS PENDING (IMPORTANT CHANGE) */
    $insertQuery = "
        INSERT INTO content
        (ContentTitle, ContentType, ContentDescription, ContentFile, UploadedBy, ApprovalStatus, UploadDate)
        VALUES
        ('$title', '$type', '$description', '$filePath', '$educatorUsername', 'Pending', NOW())
    ";

    if (mysqli_query($dbConn, $insertQuery)) {
        echo "<script>alert('Content submitted for admin approval.'); window.location='upload_content.php';</script>";
        exit;
    } else {
        echo "<script>alert('Upload failed.'); window.history.back();</script>";
        exit;
    }
}

/* Fetch educator's uploaded content (Pending + Approved) */
$contentQuery = "
    SELECT * FROM content
    WHERE UploadedBy = '$educatorUsername'
    ORDER BY UploadDate DESC
";
$contentResult = mysqli_query($dbConn, $contentQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Upload Content</title>
<link rel="stylesheet" href="upload_content.css">
</head>

<body>

<div class="main">
    <a href="Educator_Dashboard.php" class="back-btn">← Back to Dashboard</a>

    <h1>Upload Learning Content</h1>

    <!-- Upload Form -->
    <form action="upload_content.php" method="post" enctype="multipart/form-data" class="upload-form">

        <label>Title</label>
        <input type="text" name="title" required>

        <label>Type</label>
        <select name="type" required>
            <option value="Article">Article</option>
            <option value="Infographic">Infographic</option>
            <option value="Videos">Video Reference</option>
        </select>

        <label>Description</label>
        <textarea name="description" rows="5" required></textarea>

        <label>Upload File (optional)</label>
        <input type="file" name="file">

        <button type="submit" name="submit" class="btn">Submit for Approval</button>
    </form>

    <hr>

    <!-- Uploaded Content -->
    <h2>Your Uploaded Content</h2>

    <div class="discussion-grid">
        <?php while ($row = mysqli_fetch_assoc($contentResult)) : ?>
            <div class="discussion-card">
                <h3><?= $row['ContentTitle'] ?></h3>
                <p><strong>Type:</strong> <?= $row['ContentType'] ?></p>
                <p><?= $row['ContentDescription'] ?></p>

                <p>
                    <strong>Status:</strong>
                    <?= $row['ApprovalStatus'] ?>
                </p>

                <?php if (!empty($row['ContentFile'])) : ?>
                    <a href="<?= $row['ContentFile'] ?>" target="_blank">View File</a>
                <?php endif; ?>

                <small>Uploaded on <?= $row['UploadDate'] ?></small>
            </div>
        <?php endwhile; ?>
    </div>

</div>
</body>
</html>





