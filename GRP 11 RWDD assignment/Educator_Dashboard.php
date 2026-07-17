<?php
session_start();
include("conn.php");


/* Uploaded Content count */
$contentQuery = "SELECT COUNT(*) AS total FROM content";
$contentResult = mysqli_query($dbConn, $contentQuery);
$contentData = mysqli_fetch_assoc($contentResult);

/* Competitions created */
$competitionQuery = "SELECT COUNT(*) AS total FROM competition";
$competitionResult = mysqli_query($dbConn, $competitionQuery);
$competitionData = mysqli_fetch_assoc($competitionResult);

/* Active discussions */
$discussionQuery = "SELECT COUNT(*) AS total FROM discussion";
$discussionResult = mysqli_query($dbConn, $discussionQuery);
$discussionData = mysqli_fetch_assoc($discussionResult);

/* Total participants (students only) */
$participantQuery = "SELECT COUNT(*) AS total FROM users WHERE Role='Student'";
$participantResult = mysqli_query($dbConn, $participantQuery);
$participantData = mysqli_fetch_assoc($participantResult);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Educator Dashboard</title>
    <link rel="stylesheet" href="Educator_Dashboard.css">
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Educator Panel</h2>
        <ul>
            <li><a href="Educator_Dashboard.php" class="active">Dashboard</a></li>
            <li><a href="upload_content.php">Upload Content</a></li>
            <li><a href="create_competition.php">Create Quiz/Challenges/Competition</a></li>
            <li><a href="forum.php">Discussion Forum</a></li>
            <li><a href="performance.php">Participant Performance</a></li>
            <li><a href="view_content.php">View Content</a></li>
            <li><a href="upload_module.php">Upload module</a></li>
            <li><a href="view_module.php">View module</a></li>
            <li><a href="Educator_Logout.php" onclick="return confirm('Are you sure you want to logout?');">Logout</a>
</li>

        </ul>
    </div>

    <!-- Main Content -->
    <div class="main">
        <h1>Welcome, Educator</h1>
        <p class="subtitle">Manage learning content, competitions, and discussions</p>

        <!-- Statistics Cards -->
<!-- Statistics Cards -->
<div class="cards">
    <div class="card">
        <h3>Uploaded Content</h3>
        <p><?php echo $contentData['total']; ?></p>
    </div>

    <div class="card">
        <h3>Competitions Created</h3>
        <p><?php echo $competitionData['total']; ?></p>
    </div>

    <div class="card">
        <h3>Active Discussions</h3>
        <p><?php echo $discussionData['total']; ?></p>
    </div>

    <div class="card">
        <h3>Total Participants</h3>
        <p><?php echo $participantData['total']; ?></p>
    </div>
</div>


        <!-- Quick Actions -->
        <div class="actions">
            <a href="upload_content.php" class="btn">Upload New Content</a>
            <a href="create_competition.php" class="btn">Create Quiz / Challenges/ Competition</a>
            <a href="forum.php" class="btn">View Discussion Forum</a>
            <a href="view_performance.php" class="btn">View Participant Performance</a>
            <a href="view_content.php" class="btn">View Content</a>
            <a href="upload_module.php" class="btn">Upload Module</a>
            <a href="view_module.php" class="btn">View Module</a>
        </div>
    </div>

</body>
</html>
^