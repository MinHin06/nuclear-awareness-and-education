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

    $userID = (int)$_SESSION['UserID'];
    $username = $_SESSION['Username'];

    if (!isset($_GET['CompetitionID'])) {
        header("Location: student_activities.php");
    exit();
    }

    $competitionID = (int)$_GET['CompetitionID'];
    if ($competitionID <= 0) {
        header("Location: student_activities.php");
        exit();
    }

    include 'includes/header.php';

    $today = date("Y-m-d");

    /* Load activity info */
    $sqlAttempt = "SELECT * FROM competition WHERE CompetitionID = $competitionID";
    $resultAttempt = mysqli_query($dbConn, $sqlAttempt);

    if (!$resultAttempt || mysqli_num_rows($resultAttempt) == 0) {
        echo "<div class='main'><p>Activity not found.</p></div>";
        exit();
    }
    $activity = mysqli_fetch_assoc($resultAttempt);

    /* Block if closed */
    if ($activity['EndDate'] < $today) {
        echo "<link rel='stylesheet' href='student_css/studentview_question.css'>";
        echo "<div class='main'>";
        echo "<h1>" . htmlspecialchars($activity['Title']) . "</h1>";
        echo "<p class='closed'>This activity is closed. Deadline has passed.</p>";
        echo "<a class='back-btn' href='student_activities.php'>Back to Activities</a>";
        echo "</div>";
        exit();
    }

    /* Check if student already submitted for this activity */
    $sqlSubmitted = "SELECT r.ResponseID
                    FROM responses r
                    INNER JOIN questions q ON r.QuestionID = q.QuestionID
                    WHERE r.UserID = $userID AND q.CompetitionID = $competitionID
                    LIMIT 1";
    $resSubmitted = mysqli_query($dbConn, $sqlSubmitted);

    $alreadySubmitted = false;
    if ($resSubmitted && mysqli_num_rows($resSubmitted) > 0) {
        $alreadySubmitted = true;
    }

    $msg = "";

    if (isset($_GET['msg'])) {
        if ($_GET['msg'] == "success") {
            $msg = "Submitted successfully. You cannot resubmit again.";
        } else if ($_GET['msg'] == "already") {
            $msg = "You already submitted this activity. One-time submission only.";
        } else if ($_GET['msg'] == "empty") {
            $msg = "No answers submitted.";
        }
    }


    /* Load questions */
    $sqlQ = "SELECT * FROM questions
            WHERE CompetitionID = $competitionID
            ORDER BY QuestionID ASC";
    $resQ = mysqli_query($dbConn, $sqlQ);
?>

<link rel="stylesheet" href="student_css/studentview_question.css">

<div class="main">
    <h1><?php echo htmlspecialchars($activity['Title']); ?></h1>
    <p class="meta">
        <b>Type:</b> <?php echo htmlspecialchars($activity['CompetitionType']); ?> |
        <b>By:</b> <?php echo htmlspecialchars($activity['CreatedBy']); ?> |
        <b>Deadline:</b> <?php echo htmlspecialchars($activity['EndDate']); ?>
    </p>

    <?php
    if ($msg != "") {
        echo "<div class='msg'>" . htmlspecialchars($msg) . "</div>";
    }
   

    if ($alreadySubmitted) {
        echo "<div class='closed'>You have already submitted. One-time submission only.</div>";
    }
    ?>

    <form method="post" action="submit_answer.php?CompetitionID=<?php echo $competitionID; ?>">
         <input type="hidden" name="CompetitionID" value="<?php echo $competitionID; ?>">


    <?php
    if (!$resQ || mysqli_num_rows($resQ) == 0) {
         echo "<p>No questions found for this activity.</p>";
    } else {
        $num=1;
        while ($q = mysqli_fetch_assoc($resQ)) {
            $qid = (int)$q['QuestionID'];

            echo "<div class='q-card'>";
            echo "<h3>Q" . $num . ". " . htmlspecialchars($q['QuestionText']) . "</h3>";

            if ($q['QuestionType'] == "MCQ") {
                $opt1 = $q['Option1'];
                $opt2 = $q['Option2'];
                $opt3 = $q['Option3'];
                $opt4 = $q['Option4'];

                $disabled = ($alreadySubmitted) ? "disabled" : "";

                echo "<label class='opt'><input $disabled type='radio' name='answer[$qid]' value='Option1'> " . htmlspecialchars($opt1) . "</label>";
                echo "<label class='opt'><input $disabled type='radio' name='answer[$qid]' value='Option2'> " . htmlspecialchars($opt2) . "</label>";
                echo "<label class='opt'><input $disabled type='radio' name='answer[$qid]' value='Option3'> " . htmlspecialchars($opt3) . "</label>";
                echo "<label class='opt'><input $disabled type='radio' name='answer[$qid]' value='Option4'> " . htmlspecialchars($opt4) . "</label>";

            } else {
                $disabled = ($alreadySubmitted) ? "disabled" : "";
                echo "<textarea class='txt' name='answer[$qid]' placeholder='Write your answer here...' $disabled></textarea>";
            }
            echo "</div>";
            $num++;
        }
    }
    ?>

    <?php
    if (!$alreadySubmitted) {
        echo "<input class='submit-btn' type='submit' name='btnSubmit' value='Submit Answers'>";
    }
    ?>
    </form>
    <a class="back-btn" href="student_activities.php">Back to Activities</a>
</div>
<?php include "includes/footer.php"; ?>
            

