<?php
include("conn.php");
session_start();

// Logged-in user info
$userID = $_SESSION['UserID'] ?? 0;
$role = $_SESSION['role'] ?? 'Educator'; // YOUR role is EDUCATOR
$username = $_SESSION['username'] ?? 'Unknown';

if ($userID <= 0) {
    header("Location: login.html");
    exit;
}

// Get discussion ID
$discussionID = intval($_GET['id'] ?? 0);
if ($discussionID <= 0) die("Invalid discussion ID.");

// Fetch discussion
$discussionQuery = "SELECT d.*, u.Username 
                    FROM discussion d 
                    LEFT JOIN users u ON d.CreatedBy = u.UserID
                    WHERE d.DiscussionID = $discussionID";
$discussionResult = mysqli_query($dbConn, $discussionQuery);
$discussion = mysqli_fetch_assoc($discussionResult);
if (!$discussion) die("Discussion not found.");

// Handle new comment submission
if (isset($_POST['submit_comment'])) {
    $commentText = mysqli_real_escape_string($dbConn, $_POST['comment']);
    if (!empty($commentText)) {
        $insertQuery = "INSERT INTO comments (DiscussionID, UserID, CommentText, CreatedDate)
                        VALUES ($discussionID, $userID, '$commentText', NOW())";
        mysqli_query($dbConn, $insertQuery);
        header("Location: discussion_thread.php?id=$discussionID");
        exit;
    } else {
        echo "<script>alert('Comment cannot be empty.'); window.history.back();</script>";
    }
}

// Fetch comments
$commentQuery = "SELECT c.CommentID, c.CommentText, c.UserID, u.Username, u.Role, c.CreatedDate
                 FROM comments c
                 JOIN users u ON c.UserID = u.UserID
                 WHERE c.DiscussionID = $discussionID
                 ORDER BY c.CreatedDate ASC";
$commentResult = mysqli_query($dbConn, $commentQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($discussion['Title']) ?></title>
    <link rel="stylesheet" href="discussion_thread.css">
</head>
<body>
<div class="main">
    <a href="Educator_Dashboard.php" class="back-btn">← Back to Dashboard</a>

    <h1 class="discussion-title"><?= htmlspecialchars($discussion['Title']) ?></h1>
    <p class="discussion-desc"><?= htmlspecialchars($discussion['Description']) ?></p>
    <p>Posted by <strong><?= htmlspecialchars($discussion['Username'] ?? 'Unknown') ?></strong> on 
       <strong><?= date("d M Y, H:i", strtotime($discussion['CreatedDate'])) ?></strong></p>
    <hr>

    <h2>Comments</h2>
    <div class="comment-section">
        <?php if (mysqli_num_rows($commentResult) > 0): ?>
            <?php while ($comment = mysqli_fetch_assoc($commentResult)): ?>
                <div class="comment">
                    <span class="comment-user">
                        <?= htmlspecialchars($comment['Username']) ?> (<?= $comment['UserID'] == $userID ? "Educator" : htmlspecialchars($comment['Role']) ?>):
                    </span>
                    <span class="comment-text"><?= htmlspecialchars($comment['CommentText']) ?></span>
                    <span class="comment-date"><?= date("d M Y, H:i", strtotime($comment['CreatedDate'])) ?></span>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="comment"><em>No comments yet.</em></div>
        <?php endif; ?>
    </div>

    <!-- Comment form -->
    <form class="comment-form" action="" method="post">
        <textarea name="comment" placeholder="Write your comment..." required></textarea><br>
        <button type="submit" name="submit_comment" class="comment-btn">Post Comment</button>
    </form>
</div>
</body>
</html>





