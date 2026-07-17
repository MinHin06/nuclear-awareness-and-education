<?php
session_start();
include("conn.php");

/* 1) Auth check */
if (!isset($_SESSION['UserID']) || $_SESSION['Role'] !== 'Student') {
    header("Location: login.html");
    exit();
}

$userID = (int)$_SESSION['UserID'];

/* 2) Competition check */
if (!isset($_GET['CompetitionID'])) {
    header("Location: student_activities.php");
    exit();
}

$competitionID = (int)$_GET['CompetitionID'];
if ($competitionID <= 0) {
    header("Location: student_activities.php");
    exit();
}

/* 3) Prevent resubmission */
$check = "
SELECT 1
FROM leaderboards
WHERE CompetitionID = $competitionID AND UserID = $userID
LIMIT 1
";
$checkRes = mysqli_query($dbConn, $check);

if (mysqli_num_rows($checkRes) > 0) {
    header("Location: studentview_question.php?CompetitionID=$competitionID&msg=already");
    exit();
}

/* 4) Validate answers */
if (!isset($_POST['answer']) || !is_array($_POST['answer'])) {
    header("Location: studentview_question.php?CompetitionID=$competitionID&msg=empty");
    exit();
}

$totalScore = 0;

/* 5) Save responses + mark */
foreach ($_POST['answer'] as $questionID => $studentAnswer) {

    $questionID = (int)$questionID;
    $studentAnswer = trim($studentAnswer);

    if ($studentAnswer === "") {
        continue;
    }

    $qQuery = "
    SELECT QuestionType, CorrectAnswer, AnswerKey
    FROM questions
    WHERE QuestionID = $questionID AND CompetitionID = $competitionID
    ";
    $qResult = mysqli_query($dbConn, $qQuery);

    if (!$qResult || mysqli_num_rows($qResult) === 0) {
        continue;
    }

    $q = mysqli_fetch_assoc($qResult);

    /* ===== NORMALIZE STRUCTURED ANSWERS ===== */
    $answerKey = trim($q['AnswerKey']);
    // Replace multiple whitespace/newlines with single space
    $studentNorm = preg_replace('/\s+/', ' ', $studentAnswer);
    $answerKeyNorm = preg_replace('/\s+/', ' ', $answerKey);
    /* ====================================== */

    /* Auto marking logic */
    switch ($q['QuestionType']) {

        case 'MCQ':
            if ($studentAnswer === $q['CorrectAnswer']) {
                $totalScore++;
            }
            break;

        case 'STRUCTURED':
            if (strcmp($studentNorm, $answerKeyNorm) === 0) {
                $totalScore++;
            }
            break;

        case 'BOTH':
            if ($studentAnswer === $q['CorrectAnswer'] || strcmp($studentNorm, $answerKeyNorm) === 0) {
                $totalScore++;
            }
            break;
    }

    $safeAnswer = mysqli_real_escape_string($dbConn, $studentAnswer);

    mysqli_query($dbConn, "
        INSERT INTO responses (QuestionID, UserID, Answer, Timestamp)
        VALUES ($questionID, $userID, '$safeAnswer', NOW())
    ");
}

/* 6) Insert leaderboard score (rank TEMP = 0) */
mysqli_query($dbConn, "
INSERT INTO leaderboards (CompetitionID, UserID, Score, RankPosition, Timestamp)
VALUES ($competitionID, $userID, $totalScore, 0, NOW())
");

/* 7) Recalculate ranking */
$rankQuery = "
SELECT LeaderboardID
FROM leaderboards
WHERE CompetitionID = $competitionID
ORDER BY Score DESC, Timestamp ASC
";

$rankResult = mysqli_query($dbConn, $rankQuery);

$rank = 1;
while ($row = mysqli_fetch_assoc($rankResult)) {
    $lbID = (int)$row['LeaderboardID'];

    mysqli_query($dbConn, "
        UPDATE leaderboards
        SET RankPosition = $rank
        WHERE LeaderboardID = $lbID
    ");

    $rank++;
}

/* 8) Done */
header("Location: student_view_leaderboard.php?CompetitionID=$competitionID");
exit();
?>







