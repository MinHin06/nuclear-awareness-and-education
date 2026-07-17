<?php
    session_start();
    include "conn.php";

    if (!isset($_SESSION['UserID'])) {
        header("Location: login.html");
        exit();
    }
    if (!isset($_SESSION['Role']) || $_SESSION['Role'] !== 'Student') {
        header("Location: login.html");
        exit();
    }

    $userID = (int)$_SESSION['UserID'];

    /* search keyword */
    $q = "";
    if (isset($_GET['q'])) {
        $q = trim($_GET['q']);
        $q = mysqli_real_escape_string($dbConn, $q);
    }

/* build SQL (show ALL modules + this student's progress if exists)*/
    if ($q != "") {
        $sql = "SELECT m.ModuleID, m.ModuleTitle, m.ModuleDescription,m.CreatedBy, m.CreatedDate,
                     p.ProgressStatus, p.ProgressPercentage
                FROM modules m
                LEFT JOIN progress p
                ON m.ModuleID = p.ModuleID AND p.UserID = $userID
                WHERE m.ModuleTitle LIKE '%$q%'
                OR m.ModuleDescription LIKE '%$q%'
                ORDER BY m.CreatedDate DESC";
    } else {
        $sql = "SELECT m.ModuleID, m.ModuleTitle, m.ModuleDescription,m.CreatedBy, m.CreatedDate,
                     p.ProgressStatus, p.ProgressPercentage
                FROM modules m
                LEFT JOIN progress p
                ON m.ModuleID = p.ModuleID AND p.UserID = $userID
                ORDER BY m.CreatedDate DESC";
    }

    $result = mysqli_query($dbConn, $sql);


    include "includes/header.php";
    ?>

<h2>Learning Modules</h2>

<form method="GET" action="student_modules.php" class= "module-search">
    <label>Search modules:</label>
    <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Type module title...">
    <input type="submit" value="Search">
    <a href="student_modules.php">Reset</a>
</form>


<div class="module-grid">
    <?php
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $moduleID = (int)$row['ModuleID'];
                $title    = $row['ModuleTitle'];
                $createdBy = $row['CreatedBy'];
                $createdDate = $row['CreatedDate'];

                if ($row['ProgressStatus'] === NULL) {
                     // LEFT JOIN found no matching progress row
                    $statusText = "Not Started";
                    $percentText = "0%";
                } else {
                    $statusText = $row['ProgressStatus'];
                    $percentText = $row['ProgressPercentage'] . "%";
                }
    ?>

    <div class="module-card">
        <h3><?php echo htmlspecialchars($title); ?></h3>

        <p class="module-meta">
            <b>Module ID:</b> <?php echo $moduleID; ?><br>
            <b>Created By:</b> <?php echo htmlspecialchars($createdBy); ?><br>
            <b>Created Date:</b> <?php echo htmlspecialchars($createdDate); ?>
        </p>

        <p class="module-progress">
            <b>Your Progress:</b> <?php echo htmlspecialchars($statusText); ?> (<?php echo htmlspecialchars($percentText); ?>)
        </p>

        <div class="module-actions">
            <a class="btn" href="studentview_module.php?ModuleID=<?php echo $moduleID; ?>">View</a>

            <?php if ($statusText != "Completed"): ?>
                <a class="btn btn-done" href="studentview_module.php?ModuleID=<?php echo $moduleID; ?>&mark=done">Done</a>
            <?php else: ?>
                <span class="done-tag">Completed</span>
            <?php endif; ?>
        </div>
    </div>

    <?php
            }
        } else {
            echo "<p>No modules found.</p>";
        }
    ?>
</div>
        
<br>         

<a href="student_dashboard.php" class="back-btn"> Back to Dashboard</a>

<?php include "includes/footer.php"; ?>

