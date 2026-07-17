<?php
include("conn.php"); // database connection
if (isset($_POST['create'])) {
    $title = mysqli_real_escape_string($dbConn, $_POST['title']);
    $description = mysqli_real_escape_string($dbConn, $_POST['description']);
    $type = $_POST['type'];
    $start = $_POST['start'];
    $end = $_POST['end'];
    $createdBy = "Educator"; // replace with session username
    $createdDate = date("Y-m-d H:i:s");
    $approvalDate = "0000-00-00 00:00:00"; // default date for ApprovalDate

    $sql = "INSERT INTO competition 
            (CompetitionType, Title, Description, StartDate, EndDate, ApprovalStatus, CreatedBy, ApprovedBy, ContentID, CaseStudyID, CreatedDate, ApprovalDate) 
            VALUES 
            ('$type','$title','$description','$start','$end','Pending','$createdBy','','0','0','$createdDate','$approvalDate')";

    if (mysqli_query($dbConn, $sql)) {
        $competitionID = mysqli_insert_id($dbConn); // get new competition ID
        header("Location: add_question.php?id=$competitionID"); // go to add questions
        exit;
    } else {
        echo "Error: " . mysqli_error($dbConn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
        <a href="Educator_Dashboard.php" class="back-btn">← Back to Dashboard</a>
    <title>Create Quiz / Challenge / Competition</title>
    <link rel="stylesheet" href="create_competition.css">
</head>
<body>
    <h2>Create Quiz / Challenge / Competition</h2>

    <form method="post">
        <label>Title</label><br>
        <input type="text" name="title" required><br><br>

        <label>Description</label><br>
        <textarea name="description" required></textarea><br><br>

        <label>Type</label><br>
        <select name="type" required>
            <option value="Quiz">Quiz</option>
            <option value="Challenges">Challenges</option>
            <option value="Competition">Competition</option>
        </select><br><br>

        <label>Start Date</label><br>
        <input type="date" name="start" required><br><br>

        <label>End Date</label><br>
        <input type="date" name="end" required><br><br>

        <button name="create">Create</button>
    </form>
</body>
</html>
