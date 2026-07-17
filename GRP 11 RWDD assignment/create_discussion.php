<?php
include("conn.php");
session_start();


/* Get role from session */
$role = $_SESSION['role'] ?? 'Educator';
$educatorUsername = $_SESSION['username'] ?? '';

/* Allow Educator OR Student only */
if (!in_array($role, ['Educator', 'Student'], true)) {
    echo "<script>
        alert('Only Educators and Students can create discussion.');
        window.history.back();
    </script>";
    exit;
}



if(isset($_POST['submit'])){
    $title = mysqli_real_escape_string($dbConn, $_POST['title']);
    $description = mysqli_real_escape_string($dbConn, $_POST['description']);

    $insertQuery = "INSERT INTO discussion (CreatedBy, Title, Description, CreatedDate) 
                    VALUES ('$educatorUsername', '$title', '$description', NOW())";

    if(mysqli_query($dbConn, $insertQuery)){
        echo "<script>alert('Discussion created successfully!'); window.location='forum.php';</script>";
    } else {
        echo "<script>alert('Failed to create discussion.'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <a href="Educator_Dashboard.php" class="back-btn">← Back to Dashboard</a>
    <title>Create Discussion</title>
    <link rel="stylesheet" href="create_discussion.css">
</head>
<body>


    <!-- Main Content -->
    <div class="main">
        <h1>Create New Discussion</h1>
        <p class="subtitle">Start a new topic to engage students in nuclear & clean energy discussions</p>

        <div class="discussion-form-container">
            <form action="create_discussion.php" method="post" class="discussion-form">
                <label for="title">Discussion Title:</label>
                <input type="text" id="title" name="title" placeholder="Enter discussion title" required><br>

                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="6" placeholder="Write a description..." required></textarea>

                <button type="submit" name="submit" class="btn">Create Discussion</button>
            </form>
        </div>
    </div>
</body>
</html>
