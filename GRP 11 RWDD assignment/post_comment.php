<?php
include("conn.php");
session_start();

// Get logged-in user
$userID = $_SESSION['UserID'] ?? 0;

if ($userID <= 0) {
    // Not logged in
    header("Location: login.html");
    exit;
}

// Get discussion ID from GET
$discussionID = intval($_GET['discussion_id'] ?? 0);

if ($discussionID <= 0) {
    die("Invalid discussion ID.");
}

if(isset($_POST['submit'])){
    $commentText = mysqli_real_escape_string($dbConn, $_POST['comment']);

    if(!empty($commentText)){
        $insertQuery = "INSERT INTO comments (DiscussionID, UserID, CommentText, CreatedDate) 
                        VALUES ($discussionID, $userID, '$commentText', NOW())";

        if(mysqli_query($dbConn, $insertQuery)){
            // Redirect back to the same discussion thread
            header("Location: discussion_thread.php?id=$discussionID");
            exit;
        } else {
            echo "<script>alert('Failed to post comment.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Comment cannot be empty.'); window.history.back();</script>";
    }
} else {
    // Invalid access without submit
    header("Location: discussion_thread.php?id=$discussionID");
    exit;
}
?>

