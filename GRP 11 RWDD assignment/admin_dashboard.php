<?php
$pageTitle = "Admin Dashboard"; 
include "conn.php";
$conn = $dbConn;
include "admin_layout_header.php";
include "system_alert.php";

$systemAlerts = [];

$totalUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"))['total'] ?? 'N/A';
$approveContent = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM content WHERE ApprovalStatus='Approve'"))['total'] ?? 'N/A';
$activeCompetitions = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM competition WHERE EndDate >= CURDATE()"))['total'] ?? 'N/A';
$systemVersion = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MAX(AppVersion) AS version FROM updatelog"))['version'] ?? 'N/A';

$recentActivities = [];

$usersQuery = mysqli_query($conn, "
    SELECT CONCAT('User ', Username, ' added') AS activity, DateJoined AS date
    FROM users
    ORDER BY DateJoined DESC
    LIMIT 10
");
if ($usersQuery)
    while ($row = mysqli_fetch_assoc($usersQuery))
        $recentActivities[] = $row;
else
    $systemAlerts[] = "Unable to load recent users.";

$contentQuery = mysqli_query($conn, "
    SELECT CONCAT('Content ', ContentTitle, ' approved') AS activity, UploadDate AS date
    FROM content
    WHERE ApprovalStatus='Approve'
    ORDER BY UploadDate DESC
    LIMIT 10
");
if ($contentQuery)
    while ($row = mysqli_fetch_assoc($contentQuery))
        $recentActivities[] = $row;
else
    $systemAlerts[] = "Unable to load recent content.";

$compQuery = mysqli_query($conn, "
    SELECT CONCAT('Competition ', Title, ' created/updated') AS activity, StartDate AS date
    FROM competition
    ORDER BY StartDate DESC
    LIMIT 10
");
if ($compQuery)
    while ($row = mysqli_fetch_assoc($compQuery))
        $recentActivities[] = $row;
else
    $systemAlerts[] = "Unable to load recent competitions.";

$updatesQuery = mysqli_query($conn, "
    SELECT CONCAT('System updated to version ', AppVersion) AS activity, Updatedate AS date
    FROM updatelog
    ORDER BY Updatedate DESC
    LIMIT 10
");
if ($updatesQuery)
    while ($row = mysqli_fetch_assoc($updatesQuery))
        $recentActivities[] = $row;
else
    $systemAlerts[] = "Unable to load recent system updates.";

usort($recentActivities, function ($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']); });
$recentActivities = array_slice($recentActivities, 0, 10);
?>

<h1>Admin Dashboard</h1>

<div class="card-grid">
    <div class="card">
        <h3>Total Users</h3>
        <p><?= $totalUsers ?></p>
    </div>
    <div class="card">
        <h3>Resource Upload</h3>
        <p><?= $approveContent ?></p>
    </div>
    <div class="card">
        <h3>Active Module</h3>
        <p><?= $activeCompetitions ?></p>
    </div>
    <div class="card">
        <h3>System Version</h3>
        <p><?= $systemVersion ?></p>
    </div>
</div>

<div class="card card-buttons">
    <a href="user_management.php" class="btn">Manage Users</a>
    <a href="content_approval.php" class="btn">Manage Content</a>
    <a href="admin_system.php" class="btn">System Updates</a>
    <a href="activity_performance.php" class="btn">Activity Performance</a>
    <a href="badges_assign.php" class="btn">Badges Management</a>
    <a href="leaderboard.php" class="btn">Leaderboards</a>
</div>

<div class="card system-log-box">
    <h3>Recent Activities</h3>
    <div class="system-log-content">
        <?php if (count($recentActivities) > 0): ?>
            <?php foreach ($recentActivities as $act): ?>
                <div class="log-item">
                    <strong><?= date('d M Y', strtotime($act['date'])) ?></strong>
                    <p><?= $act['activity'] ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No recent activities found.</p>
        <?php endif; ?>
    </div>
</div>

<div class="card system-alert-box">
    <h3>System Alerts</h3>
    <div class="system-log-content">
        <?php if (count($systemAlerts) > 0): ?>
            <?php foreach ($systemAlerts as $alert): ?>
                <div class="alert danger"><?= $alert ?></div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert none">No system errors detected.</div>
        <?php endif; ?>
    </div>
</div>
</div>

<?php include "admin_layout_footer.php"; ?>