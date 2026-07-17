<?php
session_start();
include "conn.php"; 

if (!isset($_SESSION['UserID'])) {
    header("Location: login.html");
    exit();
}

if ($_SESSION['Role'] !== 'Student') {
    echo "<script>alert('Access denied: Students only.'); window.location.href = 'login.html';</script>";
    exit();
}

$userID = $_SESSION['UserID'];
$username = $_SESSION['Username'];

$pageCss2 = "student_css/student_view_leaderboard.css";
include 'includes/header.php';

function getLeaderboardByType($dbConn, $type) {
    $typeSafe = mysqli_real_escape_string($dbConn, $type);

    $sql = "SELECT l.CompetitionID,
                   l.Score,
                   l.Timestamp,
                   u.Username,
                   c.Title AS CompetitionTitle
            FROM leaderboards l
            LEFT JOIN users u ON l.UserID = u.UserID
            LEFT JOIN competition c ON l.CompetitionID = c.CompetitionID
            WHERE c.CompetitionType = '$typeSafe'
            ORDER BY l.CompetitionID ASC, l.Score DESC, l.Timestamp ASC";
    return mysqli_query($dbConn, $sql);
}

$resQuiz = getLeaderboardByType($dbConn, "Quiz");
$resChallenges = getLeaderboardByType($dbConn, "Challenges");
$resCompetition = getLeaderboardByType($dbConn, "Competition");
?>

<div class="lb-wrap">
    <a class="back-btn" href="student_dashboard.php">Back to Dashboard</a>
    <h2 class="page-title">Leaderboards</h2>

    <!-- ================= QUIZ ================= -->
    <div class="section-box">
        <h3 class="section-title">Quiz</h3>
        <div class="table-box">
            <table class="lb-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Username</th>
                        <th>Quiz Title</th>
                        <th>Score</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if ($resQuiz && mysqli_num_rows($resQuiz) > 0) {
                    $currentCompID = -1;
                    $rank = 0;

                    while ($row = mysqli_fetch_assoc($resQuiz)) {
                        $compID = (int)$row['CompetitionID'];

                        if ($compID != $currentCompID) {
                            $currentCompID = $compID;
                            $rank = 1;

                            echo "<tr class='group-row'>
                                    <td colspan='5'>" . htmlspecialchars($row['CompetitionTitle']) . "</td>
                                  </tr>";
                        } else {
                            $rank++;
                        }

                        echo "<tr>";
                        echo "<td>" . $rank . "</td>";
                        echo "<td>" . htmlspecialchars($row['Username']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['CompetitionTitle']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Score']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Timestamp']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No quiz records found.</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= CHALLENGES ================= -->
    <div class="section-box">
        <h3 class="section-title">Challenges</h3>
        <div class="table-box">
            <table class="lb-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Username</th>
                        <th>Challenge Title</th>
                        <th>Score</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if ($resChallenges && mysqli_num_rows($resChallenges) > 0) {
                    $currentCompID = -1;
                    $rank = 0;

                    // ✅ FIXED: must loop $resChallenges
                    while ($row = mysqli_fetch_assoc($resChallenges)) {
                        $compID = (int)$row['CompetitionID'];

                        if ($compID != $currentCompID) {
                            $currentCompID = $compID;
                            $rank = 1;

                            echo "<tr class='group-row'>
                                    <td colspan='5'>" . htmlspecialchars($row['CompetitionTitle']) . "</td>
                                  </tr>";
                        } else {
                            $rank++;
                        }

                        echo "<tr>";
                        echo "<td>" . $rank . "</td>";
                        echo "<td>" . htmlspecialchars($row['Username']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['CompetitionTitle']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Score']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Timestamp']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No challenges records found.</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= COMPETITION ================= -->
    <div class="section-box">
        <h3 class="section-title">Competition</h3>
        <div class="table-box">
            <table class="lb-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Username</th>
                        <th>Competition Title</th>
                        <th>Score</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if ($resCompetition && mysqli_num_rows($resCompetition) > 0) {
                    $currentCompID = -1;
                    $rank = 0;

                    while ($row = mysqli_fetch_assoc($resCompetition)) {
                        $compID = (int)$row['CompetitionID'];

                        if ($compID != $currentCompID) {
                            $currentCompID = $compID;
                            $rank = 1;

                            echo "<tr class='group-row'>
                                    <td colspan='5'>" . htmlspecialchars($row['CompetitionTitle']) . "</td>
                                  </tr>";
                        } else {
                            $rank++;
                        }

                        echo "<tr>";
                        echo "<td>" . $rank . "</td>";
                        echo "<td>" . htmlspecialchars($row['Username']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['CompetitionTitle']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Score']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Timestamp']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No competition records found.</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include "includes/footer.php"; ?>
