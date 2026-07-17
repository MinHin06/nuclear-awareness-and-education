<?php
    session_start();
    include "conn.php";

   if (!isset($_SESSION['UserID']) ){
        header("Location: login.html");
        exit();
    }

    if ($_SESSION['Role'] !== 'Student') {
        echo "<script>alert('Access denied: Students only.'); window.location.href = 'login.html';</script>";
        exit();
    }

    $userID = $_SESSION['UserID'];
    $username = $_SESSION['Username'];

    include 'includes/header.php';

    // get id from URL
    if (!isset($_GET['id'])) {
        die("Missing discussion id.");
    }

    $discussionID = (int)$_GET['id'];   // convert to number for safety

    /* Insert comment post */
    if (isset($_POST['btnComment'])) {

        $commentText = trim($_POST['commentText']);

        if ($commentText == "") {
            $error = "Comment cannot be empty.";
        } else {
            $commentSafe = mysqli_real_escape_string($dbConn, $commentText);

            $sqlInsert = "INSERT INTO comments (DiscussionID, UserID, CommentText, CreatedDate)
                      VALUES ($discussionID, $userID, '$commentSafe', NOW())";

            mysqli_query($dbConn, $sqlInsert);

            // Prevent double submit on refresh
            header("Location: view_discussion.php?id=" . $discussionID);
            exit();
        }
    }

    /* get discussion topic */
    $sqlTopic = "SELECT DiscussionID, CreatedBy, Title, Description, CreatedDate
             FROM discussion
             WHERE DiscussionID = $discussionID";

    $resultTopic = mysqli_query($dbConn, $sqlTopic);

    if (mysqli_num_rows($resultTopic) == 0) {
        die("Discussion not found.");
    }
    $topic = mysqli_fetch_assoc($resultTopic);

    /* get comments */
    $sqlComments = "SELECT c.CommentText, c.CreatedDate, u.Username
                FROM comments c
                LEFT JOIN users u ON c.UserID = u.UserID
                WHERE c.DiscussionID = $discussionID
                ORDER BY c.CreatedDate ASC";
    $resultComments = mysqli_query($dbConn, $sqlComments);
?>

<a class="back-btn" href="discussion.php">← Back to Discussions</a>

<div class="topic-box">
    <h2><?php echo htmlspecialchars($topic['Title']); ?></h2>

    <p class="topic-date">
        Posted by: <?php echo htmlspecialchars($topic['CreatedBy']); ?> |
        Date: <?php echo $topic['CreatedDate']; ?>
    </p>

    <p class="topic-content">
        <?php echo nl2br(htmlspecialchars($topic['Description'])); ?>
    </p>

    <p class="topic-date">Posted: <?php echo $topic['CreatedDate']; ?></p>
</div>

<div class="comment-box">
    <h3>comments</h3>
    <?php
    if (mysqli_num_rows($resultComments) > 0) {
        while ($c = mysqli_fetch_assoc($resultComments)) {
            $commentuser = $c['Username'] ? $c['Username'] : "User";
            ?>
            <div class="comment-item">
                <div class="comment-user"><?php echo htmlspecialchars($commentuser); ?></div>
                <div class="comment-text"><?php echo nl2br(htmlspecialchars($c['CommentText'])); ?></div>
                <div class="comment-date"><?php echo $c['CreatedDate']; ?></div>
            </div>
            <?php
        }
    } else {
        echo "<p>No comments yet. Be the first!</p>";
    }
    ?>

</div>

<div class="add-comment">
    <h3>Add a comment</h3>
    <?php if (isset($error)) { ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php } ?>

    <form action="" method="post">
        <textarea name="commentText" rows="4" class="textarea"
        placeholder="Write your comment..."></textarea>

    <br><br>

        <button type="submit" name="btnComment" class="btn">
            Post Comment
        </button>
    </form>
</div>

<?php include "includes/footer.php"; ?>
