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

    /*Points earned*/
$sqlPoints = "
SELECT 
    pl.PoolName,
    p.PrizeName,
    p.PrizeDescription,
    p.PrizeValue,
    l.Score,
    l.RankPosition,
    c.Title AS CompetitionTitle,
    c.CompetitionType,
    l.Timestamp AS AwardDate
FROM prizepool pl
INNER JOIN prizes p ON pl.PrizePoolID = p.PrizePoolID
INNER JOIN competition c ON pl.CompetitionID = c.CompetitionID
INNER JOIN leaderboards l 
        ON l.CompetitionID = c.CompetitionID 
        AND l.UserID = $userID
ORDER BY l.Score DESC, l.Timestamp ASC
";

$resultPoints = mysqli_query($dbConn, $sqlPoints);



    /*Badges earned*/
    $sqlBadges = "SELECT 
                    ba.BadgeAwardedID,
                    ba.AwardDate,
                    b.BadgeName,
                    b.BadgeDescription,
                    b.BadgeCriteria,
                    b.BadgeImage,
                    c.Title AS CompetitionTitle,
                    c.CompetitionType
                FROM badgesawarded ba
                INNER JOIN badges b ON ba.BadgeID = b.BadgeID
                INNER JOIN competition c ON b.CompetitionID = c.CompetitionID
                WHERE ba.UserID = $userID
                ORDER BY ba.AwardDate DESC";
    $resultBadges = mysqli_query($dbConn, $sqlBadges);
?>

<div class="main">
    <h1>Points&Badges</h1>
    <p class ="subtitle">View your earned points and badges</p>

    <input type="text" id="searchInput" class="search-box" placeholder="Search points or badges by name...">

    <!--Prizes Section -->
<div class="section">
    <div class="section-header">
        <h2>Points (Prizes Reward)</h2>
        <p class="section-desc">These are prizes associated with competitions you participated in.</p>
    </div>

    <div class="card-grid" id="pointsGrid">
        <?php
        if ($resultPoints && mysqli_num_rows($resultPoints) > 0) {
            while ($row = mysqli_fetch_assoc($resultPoints)) {
                $poolName   = $row['PoolName'];        // From prizepool
                $name       = $row['PrizeName'];       // From prizes
                $desc       = $row['PrizeDescription'];
                $value      = $row['PrizeValue'];

                $compTitle  = $row['CompetitionTitle']; // From competition
                $compType   = $row['CompetitionType'];

                $score      = $row['Score'];           // From leaderboard
                $rankPos    = $row['RankPosition'];    // From leaderboard
                $awardDate  = $row['AwardDate'];       // Leaderboard timestamp

                // For search/filter
                $searchText = strtolower($poolName . " " . $name . " " . $compTitle . " " . $compType);

                echo '<div class="card points-card" data-search="' . htmlspecialchars($searchText) . '">';
                    echo '<div class="card-top">';
                        echo '<h3 class="card-title">' . htmlspecialchars($name) . '</h3>';
                        echo '<span class="pill pill-points">Points</span>';
                    echo '</div>';

                    echo '<p class="meta"><b>Competition:</b> ' . htmlspecialchars($compTitle) .
                        ' <span class="muted">(' . htmlspecialchars($compType) . ')</span></p>';

                    echo '<p class="meta"><b>Pool:</b> ' . htmlspecialchars($poolName) . '</p>';

                    echo '<p class="desc">' . htmlspecialchars($desc) . '</p>';

                    echo '<div class="info-row">';
                        echo '<div><b>Value:</b> ' . htmlspecialchars($value) . '</div>';
                        echo '<div><b>Leaderboard Score:</b> ' . htmlspecialchars($score) . '</div>';
                        echo '<div><b>Leaderboard Rank:</b> ' . htmlspecialchars($rankPos) . '</div>';
                    echo '</div>';

                    echo '<p class="small muted">Leaderboard updated on ' . htmlspecialchars($awardDate) . '</p>';
                echo '</div>';
            }
        } else {
            echo '<p>No points (prizes) found yet.</p>';
        }
        ?>
    </div>
</div>
         <!--Badges Section -->
         <div class="section">
            <div class="section-header">
                <h2>Badges Earned</h2>
                <p class="section-desc">Badges reflect your achievements and milestones.</p>
            </div>

            <div class="card-grid" id="badgesGrid">
                <?php
                    if ($resultBadges && mysqli_num_rows($resultBadges) > 0) {
                        while ($row = mysqli_fetch_assoc($resultBadges)) {
                            $name = $row['BadgeName'];
                            $desc = $row['BadgeDescription'];
                            $img  = $row['BadgeImage'];

                            $compTitle = $row['CompetitionTitle'];
                            $compType  = $row['CompetitionType'];

                            $awardDate = $row['AwardDate'];

                            $searchText = strtolower($name . " " . $compTitle . " " . $compType);

                             echo '<div class="card badge-card" data-search="' . htmlspecialchars($searchText) . '">';
                                echo '<div class="card-top">';
                                    echo '<h3 class="card-title">' . htmlspecialchars($name) . '</h3>';
                                    echo '<span class="pill pill-badge">Badge</span>';
                                echo '</div>';

                                if (!empty($img)) {
                                    echo '<div class="badge-img-wrap">';
                                        echo '<img class="badge-img" src="uploads/badges_image/' . htmlspecialchars($img) . '" alt="Badge">';
                                    echo '</div>';
                                }

                                echo '<p class="meta"><b>Competition:</b> ' . htmlspecialchars($compTitle) .
                                    ' <span class="muted">(' . htmlspecialchars($compType) . ')</span></p>';
                                
                                echo '<p class="desc">' . htmlspecialchars($desc) . '</p>';

                                echo '<p class="small muted">Awarded on ' . htmlspecialchars($awardDate) . '</p>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No badges earned yet.</p>';
                    }
                ?>
            </div>

            <p class="empty" id="badgesNoMatch" style="display:none;">No matching badges found.</p>

            <div>
                 <a href="student_dashboard.php" class="back-btn">← Back to Dashboard</a>
            </div>
        </div>      
        
    </div>

</div>

<script src="student_points_badges.js" defer></script>

<?php include "includes/footer.php"; ?>
