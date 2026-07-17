<?php
$pageTitle = "Activity Performance"; 
include "conn.php";
$conn = $dbConn;
include "admin_layout_header.php";
include_once "system_alert.php";


/* ================= DATE FILTER ================= */
$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';

$userDateFilter = "";
$competitionDateFilter = "";

if ($startDate && $endDate) {
    $userDateFilter = " AND DateJoined BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
    $competitionDateFilter = " AND ApprovalDate BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
}

/* ================= STATS ================= */
$activeUsers = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) total FROM users WHERE Status='Active' $userDateFilter"
))['total'];

$completedProgress = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) total FROM progress WHERE ProgressStatus='Completed'"
))['total'];

$badgesAssigned = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) total FROM badgesawarded"
))['total'];

/* ================= USER ROLE DATA ================= */
$roleData = mysqli_query(
    $conn,
    "SELECT Role, COUNT(*) total FROM users WHERE 1 $userDateFilter GROUP BY Role"
);
$roleStats = [];
while ($r = mysqli_fetch_assoc($roleData)) {
    $roleStats[] = $r;
}

/* ================= USER STATUS DATA ================= */
$statusData = mysqli_query(
    $conn,
    "SELECT Status, COUNT(*) total FROM users WHERE 1 $userDateFilter GROUP BY Status"
);
$statusStats = [];
while ($s = mysqli_fetch_assoc($statusData)) {
    $statusStats[] = $s;
}

/* ================= COMPETITION DATA ================= */
$competitionData = mysqli_query($conn, "
    SELECT 
        competition.Title,
        competition.ApprovalDate,
        COUNT(leaderboards.UserID) participants
    FROM competition
    LEFT JOIN leaderboards
        ON competition.CompetitionID = leaderboards.CompetitionID
    WHERE ApprovalDate IS NOT NULL
    $competitionDateFilter
    GROUP BY competition.CompetitionID
    ORDER BY ApprovalDate DESC
");
$competitionStats = [];
while ($c = mysqli_fetch_assoc($competitionData)) {
    $competitionStats[] = $c;
}
?>

<div class="main">

    <h1>Activity Performance</h1>

    <div class="card-container">
        <div class="card">
            <h3>Active Users</h3>
            <p><?= $activeUsers ?></p>
        </div>
        <div class="card">
            <h3>Completed Progress</h3>
            <p><?= $completedProgress ?></p>
        </div>
        <div class="card">
            <h3>Badges Assigned</h3>
            <p><?= $badgesAssigned ?></p>
        </div>
    </div>

    <h1>Reports</h1>

    <form method="get" class="filter-bar">
        <label>From:</label>
        <input type="date" name="start_date" value="<?= $startDate ?>">
        <label>To:</label>
        <input type="date" name="end_date" value="<?= $endDate ?>">
        <button class="btn small">Apply</button>
        <button type="button" class="btn small danger" onclick="window.location='activity_performance.php'">
            Reset
        </button>
    </form>

    <div class="card-container">
        <div class="card">
            <h3>User Role Distribution</h3>
            <button class="btn small" onclick="openModal('roleModal')">View Report</button>
        </div>

        <div class="card">
            <h3>User Activity</h3>
            <button class="btn small" onclick="openModal('statusModal')">View Report</button>
        </div>

        <div class="card">
            <h3>Competition Participation</h3>
            <button class="btn small" onclick="openModal('competitionModal')">View Report</button>
        </div>
    </div>
</div>

<!-- ================= ROLE MODAL ================= -->
<div id="roleModal" class="modal">
    <div class="modal-content">
        <h3>User Role Distribution</h3>

        <table class="data-table">
            <tr>
                <th>Role</th>
                <th>Total</th>
            </tr>
            <?php foreach ($roleStats as $r) { ?>
                <tr>
                    <td><?= htmlspecialchars($r['Role']) ?></td>
                    <td><?= $r['total'] ?></td>
                </tr>
            <?php } ?>
        </table>

        <div class="bar-chart">
            <?php foreach ($roleStats as $r) { ?>
                <div class="bar-row">
                    <span><?= htmlspecialchars($r['Role']) ?></span>
                    <div class="bar-fill" style="width:<?= $r['total'] * 20 ?>px"></div>
                </div>
            <?php } ?>
        </div>

        <a class="btn" href="export_user_role.php?start_date=<?= $startDate ?>&end_date=<?= $endDate ?>">Export</a>
        <button class="btn danger" onclick="closeModal('roleModal')">Close</button>
    </div>
</div>

<!-- ================= STATUS MODAL ================= -->
<div id="statusModal" class="modal">
    <div class="modal-content">
        <h3>User Activity</h3>

        <table class="data-table">
            <tr>
                <th>Status</th>
                <th>Total</th>
            </tr>
            <?php foreach ($statusStats as $s) { ?>
                <tr>
                    <td><?= htmlspecialchars($s['Status']) ?></td>
                    <td><?= $s['total'] ?></td>
                </tr>
            <?php } ?>
        </table>

        <div class="bar-chart">
            <?php foreach ($statusStats as $s) { ?>
                <div class="bar-row">
                    <span><?= htmlspecialchars($s['Status']) ?></span>
                    <div class="bar-fill" style="width:<?= $s['total'] * 20 ?>px"></div>
                </div>
            <?php } ?>
        </div>

        <a class="btn" href="export_user_activity.php?start_date=<?= $startDate ?>&end_date=<?= $endDate ?>">Export</a>
        <button class="btn danger" onclick="closeModal('statusModal')">Close</button>
    </div>
</div>

<!-- ================= COMPETITION MODAL ================= -->
<div id="competitionModal" class="modal">
    <div class="modal-content">
    <h3>Competition Participation</h3>

    <div class="modal-body-scroll">

        <table class="data-table">
            <tr>
                <th>Competition</th>
                <th>Date</th>
                <th>Participants</th>
            </tr>
            <?php foreach ($competitionStats as $c) { ?>
                <tr>
                    <td><?= htmlspecialchars($c['Title']) ?></td>
                    <td><?= date('d M Y', strtotime($c['ApprovalDate'])) ?></td>
                    <td><?= $c['participants'] ?></td>
                </tr>
            <?php } ?>
        </table>

        <div class="bar-chart">
            <?php foreach ($competitionStats as $c) { ?>
                <div class="bar-row">
                    <span><?= htmlspecialchars($c['Title']) ?></span>
                    <div class="bar-fill" style="width:<?= $c['participants'] * 20 ?>px"></div>
                </div>
            <?php } ?>
        </div>

    </div>

    <a class="btn" href="export_competition_report.php?start_date=<?= $startDate ?>&end_date=<?= $endDate ?>">
        Export
    </a>
    <button class="btn danger" onclick="closeModal('competitionModal')">Close</button>
    </div>
</div>

<?php include "admin_layout_footer.php"; ?>
