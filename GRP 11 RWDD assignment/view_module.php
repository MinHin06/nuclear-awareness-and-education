<?php
include("conn.php");
session_start();

// Anyone can view modules (Educator / Student / Guest)
$query = "SELECT * FROM modules ORDER BY CreatedDate DESC";
$result = mysqli_query($dbConn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Modules</title>
    <link rel="stylesheet" href="view_module.css">
</head>
<body>


<div class="main">
    <a href="Educator_Dashboard.php" class="back-btn">← Back to Dashboard</a>

    <h1>Course Modules</h1>
    <p class="subtitle">Browse all modules uploaded by educators</p>

    <!-- Search box -->
    <input type="text" id="searchInput" class="search-box" placeholder="Search by module title...">

    <!-- Modules List -->
    <div class="module-grid">

        <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="module-card module-box"
                 data-title="<?= strtolower($row['ModuleTitle']); ?>">

                <h3><?= $row['ModuleTitle']; ?></h3>

                <p class="content-desc"><?= $row['ModuleDescription']; ?></p>

                <button onclick="toggleDesc(this)" class="small-btn">
                    Show / Hide Description
                </button>

                <small>
                    Created by <?= $row['CreatedBy']; ?> |
                    <?= $row['CreatedDate']; ?>
                </small>

            </div>
        <?php endwhile; ?>

        <?php if(mysqli_num_rows($result) == 0): ?>
            <p>No modules found.</p>
        <?php endif; ?>

    </div>

    <br>
    <a href="Educator_Dashboard.php" class="back-btn">← Back to Dashboard</a>

</div>

<!-- SIMPLE JAVASCRIPT -->
<script>
// Search by title
document.getElementById("searchInput").addEventListener("keyup", function () {
    let input = this.value.toLowerCase();
    let boxes = document.querySelectorAll(".module-box");

    boxes.forEach(box => {
        let title = box.getAttribute("data-title");
        box.style.display = title.includes(input) ? "block" : "none";
    });
});

// Show / hide description
function toggleDesc(button) {
    let desc = button.previousElementSibling;
    desc.style.display = (desc.style.display === "none") ? "block" : "none";
}
</script>

</body>
</html>

