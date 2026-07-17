<?php
include("conn.php");
session_start();


// Only show APPROVED content
$query = "SELECT * FROM content 
          WHERE ApprovalStatus = 'Approve'
          ORDER BY UploadDate DESC";

$result = mysqli_query($dbConn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Uploaded Content</title>
    <link rel="stylesheet" href="view_content.css">
</head>
<body>

<div class="main">
    <a href="Educator_Dashboard.php" class="back-btn">← Back to Dashboard</a>

    <h1>Educational Content</h1>
    <p class="subtitle">Browse verified educational materials</p>

    <!-- Search box -->
    <input
        type="text"
        id="searchInput"
        class="search-box"
        placeholder="Search by title..."
    >

    <!-- Content Grid -->
    <div class="discussion-grid">

        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>

                <div class="discussion-card content-box"
                     data-title="<?= strtolower($row['ContentTitle']); ?>">

                    <h3><?= htmlspecialchars($row['ContentTitle']); ?></h3>

                    <p><strong>Type:</strong> <?= htmlspecialchars($row['ContentType']); ?></p>

                    <p class="content-desc">
                        <?= nl2br(htmlspecialchars($row['ContentDescription'])); ?>
                    </p>

                    <button class="small-btn" onclick="toggleDesc(this)">
                        Show / Hide Description
                    </button>

                    <?php if (!empty($row['ContentFile'])): ?>
                        <p>
                            <a href="<?= htmlspecialchars($row['ContentFile']); ?>" target="_blank">
                                View Attachment
                            </a>
                        </p>
                    <?php endif; ?>

                    <small>
                        Uploaded by <?= htmlspecialchars($row['UploadedBy']); ?> |
                        <?= htmlspecialchars($row['UploadDate']); ?>
                    </small>

                </div>

            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-content">
                No approved content available at the moment.
            </p>
        <?php endif; ?>

    </div>

    <a href="Educator_Dashboard.php" class="back-btn">← Back to Dashboard</a>

</div>

<!-- JavaScript -->
<script>
// Search by title
document.getElementById("searchInput").addEventListener("keyup", function () {
    const input = this.value.toLowerCase();
    const boxes = document.querySelectorAll(".content-box");

    boxes.forEach(box => {
        const title = box.dataset.title;
        box.style.display = title.includes(input) ? "block" : "none";
    });
});

// Toggle description
function toggleDesc(button) {
    const desc = button.previousElementSibling;
    desc.style.display = (desc.style.display === "none") ? "block" : "none";
}
</script>

</body>
</html>

