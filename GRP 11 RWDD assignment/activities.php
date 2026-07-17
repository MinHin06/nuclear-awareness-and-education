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

    $today = date("Y-m-d");

    $sql = "SELECT CompetitionID, CompetitionType, Title, Description, StartDate, EndDate, CreatedBy, CreatedDate
            FROM competition
            WHERE ApprovalStatus = 'Pending'
            AND StartDate <= '$today'
            AND EndDate >= '$today'
            ORDER BY StartDate ASC";
    $result = mysqli_query($dbConn, $sql);

    $quizzes = array();
    $challenges = array();
    $competitions = array();

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['CompetitionType'] == "Quiz") {
                $quizzes[] = $row;
            } elseif ($row['CompetitionType'] == "Challenges") {
                $challenges[] = $row;
            } else {
                $competitions[] = $row;
            }
        }
    }

?>
    

<div class="main">
    <h1>Activities</h1>
    <p class="subtitle">Join ongoing quizzes, challenges, and competitions before the deadline.</p>
    

    <div class="search-wrap">
        <input type="text" id="searchInput" class="search-box" placeholder="Search activity title...">
    </div>

    <!-- Quizzes Section -->
    <h2 class="section-title">Quiz</h2>
    <div class="card-grid" id="quizgrid">
        <?php
        if (count($quizzes) == 0) {
            echo "<p class='empty'>No ongoing quizzes.</p>";
        } else {
             for ($i = 0; $i < count($quizzes); $i++) {
                $a = $quizzes[$i];
                echo "<div class='card activity-box' data-title='" . strtolower($a['Title']) . "'>";
                echo "<h3>" . htmlspecialchars($a['Title']) . "</h3>";
                echo "<p class='desc'>" . htmlspecialchars($a['Description']) . "</p>";
                echo "<p><b>By:</b> " . htmlspecialchars($a['CreatedBy']) . "</p>";
                echo "<p><b>Duration:</b> " . htmlspecialchars($a['StartDate']) . " to " . htmlspecialchars($a['EndDate']) . "</p>";
                echo "<a class='btn' href='studentview_question.php?CompetitionID=" . (int)$a['CompetitionID'] . "'>Attempt</a>";
                echo "</div>";
             }
        }
        ?>
    </div>

    <!-- Challenges Section -->
    <h2 class="section-title">Challenges</h2>
    <div class="card-grid" id="challengegrid">
        <?php
        if (count($challenges) == 0) {
            echo "<p class='empty'>No ongoing challenges.</p>";
        } else {
             for ($i = 0; $i < count($challenges); $i++) {
                $a = $challenges[$i];
                echo "<div class='card activity-box' data-title='" . strtolower($a['Title']) . "'>";
                echo "<h3>" . htmlspecialchars($a['Title']) . "</h3>";
                echo "<p class='desc'>" . htmlspecialchars($a['Description']) . "</p>";
                echo "<p><b>By:</b> " . htmlspecialchars($a['CreatedBy']) . "</p>";
                echo "<p><b>Duration:</b> " . htmlspecialchars($a['StartDate']) . " to " . htmlspecialchars($a['EndDate']) . "</p>";
                echo "<a class='btn' href='studentview_question.php?CompetitionID=" . (int)$a['CompetitionID'] . "'>Attempt</a>";
                echo "</div>";
             }
        }
        ?>
    </div>


    <!-- Competitions Section -->`
    <h2 class="section-title">Competitions</h2>
    <div class="card-grid" id="competitiongrid">
        <?php
        if (count($competitions) == 0) {
            echo "<p class='empty'>No ongoing competitions.</p>";
        } else {
             for ($i = 0; $i < count($competitions); $i++) {
                $a = $competitions[$i];
                echo "<div class='card activity-box' data-title='" . strtolower($a['Title']) . "'>";
                echo "<h3>" . htmlspecialchars($a['Title']) . "</h3>";
                echo "<p class='desc'>" . htmlspecialchars($a['Description']) . "</p>";
                echo "<p><b>By:</b> " . htmlspecialchars($a['CreatedBy']) . "</p>";
                echo "<p><b>Duration:</b> " . htmlspecialchars($a['StartDate']) . " to " . htmlspecialchars($a['EndDate']) . "</p>";
                echo "<a class='btn' href='studentview_question.php?CompetitionID=" . (int)$a['CompetitionID'] . "'>Attempt</a>";
                echo "</div>";
            }
        }
        ?>
    </div>

    <a class="back-btn" href="student_dashboard.php">← Back to Dashboard</a>
</div>

<script src="js/activities.js"></script>
<?php include "includes/footer.php"; ?>



