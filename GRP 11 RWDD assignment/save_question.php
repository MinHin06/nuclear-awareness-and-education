<?php
include("conn.php"); // Your DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = mysqli_real_escape_string($dbConn, $_POST['title']);
    $description = mysqli_real_escape_string($dbConn, $_POST['description']);
    $type = $_POST['type']; // Quiz / Challenges / Competition
    $start = $_POST['start'];
    $end = $_POST['end'];
    $createdBy = "EducatorName"; // Replace with session username if logged in
    $createdDate = date("Y-m-d H:i:s");

    // Insert into competition table
    $sql = "INSERT INTO competition 
            (CompetitionType, Title, Description, StartDate, EndDate, ApprovalStatus, CreatedBy, ApprovedBy, ContentID, CaseStudyID, CreatedDate, ApprovalDate) 
            VALUES 
            ('$type', '$title', '$description', '$start', '$end', 'Pending', '$createdBy', '', 0, 0, '$createdDate', NULL)";

    if (mysqli_query($dbConn, $sql)) {
        $competitionID = mysqli_insert_id($dbConn); // Get the inserted competition ID

        // If there are questions, you can loop and insert into questions table here
        // Example for MCQ:
        /*
        foreach($_POST['questions'] as $q) {
            $questionText = mysqli_real_escape_string($dbConn, $q['text']);
            $option1 = mysqli_real_escape_string($dbConn, $q['option1']);
            $option2 = mysqli_real_escape_string($dbConn, $q['option2']);
            $option3 = mysqli_real_escape_string($dbConn, $q['option3']);
            $option4 = mysqli_real_escape_string($dbConn, $q['option4']);
            $correct = mysqli_real_escape_string($dbConn, $q['correct']);
            $createdDate = date("Y-m-d H:i:s");

            $sqlQ = "INSERT INTO questions 
                     (CompetitionID, CompetitionType, QuestionText, QuestionType, Option1, Option2, Option3, Option4, CorrectAnswer, AnswerKey, CreatedDate)
                     VALUES 
                     ('$competitionID', '$type', '$questionText', 'MCQ', '$option1', '$option2', '$option3', '$option4', '$correct', '', '$createdDate')";
            mysqli_query($dbConn, $sqlQ);
        }
        */

        echo "Competition created successfully!";
        // Redirect to dashboard or edit page
        header("Location: Educator_Dashboard.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($dbConn);
    }
}
?>
