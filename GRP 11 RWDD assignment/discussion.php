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
    $pageCss2 = "css/discussion.css";
    include "includes/header.php";

    $keyword = "";
    if (isset($_GET['q'])) {
        $keyword = trim($_GET['q']);
        $keywordSafe = mysqli_real_escape_string($dbConn, $keyword);

        $sql = "SELECT DiscussionID, Title, CreatedDate
            FROM discussion
            WHERE Title LIKE '%$keywordSafe%'
            ORDER BY CreatedDate DESC";
        } else {
            $sql = "SELECT DiscussionID, Title, CreatedDate
            FROM discussion
            ORDER BY CreatedDate DESC";
    }

    $result = mysqli_query($dbConn, $sql);
?>

<h2 class="page-title">Discussion Forum</h2>

<a href="student_dashboard.php" class="back-dashboard-btn">
    Back to Dashboard
</a>

<!-- Search bar -->
<form method="get" class="search-bar">
    <input type="text" name="q" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Search by title...">
    <button type="submit">Search</button>

    <!-- optional clear button -->
    <a class="clear-btn" href="discussion.php">Clear</a>
</form>

<div class="list">
    <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
            $id = $row['DiscussionID'];
            $title = $row['Title'];
            $date = $row['CreatedDate'];
        ?>
        <a class="topic-card" href="view_discussion.php?id=<?php echo $id; ?>">
            <div class="topic-title"><?php echo htmlspecialchars($title); ?></div>
            <div class="topic-meta">Posted: <?php echo $date; ?></div>
            <div class="topic-action">View & Comment</div>
        </a>
    <?php
        }
    } else {
        echo "<p>No discussion found.</p>";
    }
    ?>
</div>

<?php include "includes/footer.php"; ?>


