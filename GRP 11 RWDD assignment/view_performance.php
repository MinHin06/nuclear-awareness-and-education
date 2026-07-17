<?php
include("conn.php");
session_start();

/* Fetch ONLY Competition results */
$query = "
SELECT 
    u.Username,
    c.Title AS CompetitionTitle,
    l.Score,
    l.RankPosition,
    l.Timestamp
FROM leaderboards l
INNER JOIN users u ON l.UserID = u.UserID
INNER JOIN competition c ON l.CompetitionID = c.CompetitionID
WHERE c.CompetitionType = 'Competition'
ORDER BY l.RankPosition ASC, l.Score DESC
";

$result = mysqli_query($dbConn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Participant Performance</title>
    <link rel="stylesheet" href="view_performance.css">
</head>
<body>

<div class="main-content">
    <div class="main">
        <a href="Educator_Dashboard.php" class="back-btn">← Back to Dashboard</a>

        <h2>Competition Performance Records</h2>

        <table class="performance-table">
            <tr>
                <th>Student</th>
                <th>Competition</th>
                <th>Score</th>
                <th>Rank</th>
                <th>Date</th>
            </tr>

            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Username']) ?></td>
                        <td><?= htmlspecialchars($row['CompetitionTitle']) ?></td>
                        <td><?= htmlspecialchars($row['Score']) ?></td>
                        <td><?= htmlspecialchars($row['RankPosition']) ?></td>
                        <td><?= htmlspecialchars($row['Timestamp']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center;">No competition records found.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</div>

</body>
</html>

