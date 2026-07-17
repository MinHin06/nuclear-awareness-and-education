<?php
$pageTitle = "System Performance"; 
include "conn.php";
$conn = $dbConn;
include "admin_layout_header.php";
include_once "system_alert.php";

if (isset($_POST['add_log'])) {
    $date = $_POST['updatedate'] ?? '';
    $desc = $_POST['description'] ?? '';

    if ($date && $desc) {
        $desc = htmlspecialchars($desc);

        $result = mysqli_query($conn, "SELECT MAX(AppVersion) AS latestVersion FROM updatelog");
        $row = mysqli_fetch_assoc($result);
        $nextVersion = ($row['latestVersion'] ?? 0) + 1;

        $stmt = mysqli_prepare($conn, "INSERT INTO updatelog (AppVersion, Updatedate, Description) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iss", $nextVersion, $date, $desc);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

$updates = mysqli_query($conn, "SELECT * FROM updatelog ORDER BY Updatedate DESC");
?>

<div class="main">
    <h1 class="page-title">System Performance</h1>

    <div class="system-log-box">

        <div class="system-log-header"
            style=>
            <h2>System Logs</h2>
            <button class="btn accent" onclick="window.location.reload()">Refresh System</button>
        </div>

        <div class="system-log-content">
            <?php while ($u = mysqli_fetch_assoc($updates)) { ?>
                <div class="log-item">
                    <strong>Version:</strong> <?= htmlspecialchars($u['AppVersion']) ?><br>
                    <strong>Updated:</strong> <?= date('d M Y', strtotime($u['Updatedate'])) ?><br><br>
                    <strong>Description:</strong><br>
                    <?= nl2br(htmlspecialchars($u['Description'])) ?>
                </div>
            <?php } ?>
        </div>

        <div class="system-log-footer">

            <h3>Add System Version</h3>

            <form method="post" class="add-log-form">
                <label>Update Date:</label>
                <input type="date" name="updatedate" required value="<?= date('Y-m-d') ?>">

                <label>Description:</label>
                <textarea name="description" placeholder="Description here" required></textarea>

                <button name="add_log" class="btn">Save Version</button>
            </form>
        </div>

    </div>
</div>

<?php include "admin_layout_footer.php"; ?>