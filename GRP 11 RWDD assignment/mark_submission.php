<?php
session_start();
include("conn.php");

/* Security */
if (!isset($_SESSION['UserID']) || $_SESSION['Role'] !== 'Student') {
    header("Location: login.html");
    exit();
}

$userID = (int)$_SESSION['UserID'];

if (!isset($_GET['CompetitionID'])) {
    header("Location: activities.php");
    exit();
}

$competitionID = (int)$_GET['CompetitionID'];

/* Prevent duplicate leaderboard entries */
$checkLB = "
    SELECT * FROM leaderboards 
    WHERE CompetitionID = $competitionID 
    AND UserID = $userID
";
$resLB = mysqli_query($dbConn, $checkLB);

if (mysqli_num_rows($resLB) > 0) {
    header("Location: leaderboard.php?CompetitionID=$competitionID");
    exit();
}

/* Fetch student responses + correct answers */
$sql = "
    SELECT r.Answer, q.CorrectAnswer, q.QuestionType
    FROM responses r
    INNER JOIN questions q ON r.QuestionID = q.QuestionID
    WHERE r.UserID = $userID
    AND q.CompetitionID = $competitionID
";

$result = mysqli_query($dbConn, $sql);

$totalScore = 0;

/* Auto marking */
while ($row = mysqli_fetch_assoc($result)) {

    /* MCQ auto mark */
    if ($row['QuestionType'] === 'MCQ') {
        if (trim($row['Answer']) === trim($row['CorrectAnswer'])) {
            $totalScore += 1;
        }
    }

    /* Structured questions are manual-marked later */
}

/* Insert leaderboard */
$insertLB = "
    INSERT INTO leaderboards (CompetitionID, UserID, Score, Timestamp)
    VALUES ($competitionID, $userID, $totalScore, NOW())
";
mysqli_query($dbConn, $insertLB);

/* Redirect */
header("Location: leaderboard.php?CompetitionID=$competitionID");
exit();
?>
