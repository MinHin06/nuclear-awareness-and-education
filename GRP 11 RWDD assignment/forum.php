<?php
include("conn.php");
session_start();

/* Check if user is logged in */
/* Get role from session */
$role = $_SESSION['role'] ?? 'Educator';
$educatorUsername = $_SESSION['username'] ?? '';

if ($role !== "Educator") {
    echo "<script>alert('Only Educators can upload content.'); window.history.back();</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Discussion Forum</title>
    <link rel="stylesheet" href="forum.css">
</head>
<body>

    <!-- Main content -->
    <div class="main">
        <a href="Educator_Dashboard.php" class="back-btn">← Back to Dashboard</a>

        <h1>Q&A Discussion</h1>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab-button active" onclick="filterDiscussions('all')">All Questions</button>
            <button class="tab-button" onclick="filterDiscussions('recent')">Recent Questions</button>
                    <!-- Create Discussion Button (Educators Only) -->
        <?php if($role == "Educator"): ?>
            <a href="create_discussion.php" class="btn create-discussion-btn">+ Create Discussion</a>
        <?php endif; ?>
        </div>

        <!-- Discussion Cards Container -->
        <div class="discussion-grid">
            <?php
            $query = "SELECT * FROM discussion ORDER BY CreatedDate DESC";
            $result = mysqli_query($dbConn, $query);

            while($row = mysqli_fetch_assoc($result)):
                $discussionID = $row['DiscussionID'];
            ?>
                <div class="discussion-card">
                    <h3><?= htmlspecialchars($row['Title']) ?></h3>
                    <p><?= htmlspecialchars($row['Description']) ?></p>

                    <?php if($role != 'Guest'): ?>
                        <!-- Join/View button -->
                        <a href="discussion_thread.php?id=<?= $discussionID ?>" class="join-btn">Join / View</a>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        function filterDiscussions(filter) {
            // Placeholder for future AJAX filtering
            if(filter === 'recent') {
                alert('Show recent questions (latest first)');
            } else {
                alert('Show all questions');
            }

            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
        }
    </script>

</body>
</html>
