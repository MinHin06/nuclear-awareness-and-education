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

    if (!isset($_GET['ModuleID'])) {
        header("Location: student_modules.php");
        exit();
    }
    $moduleID = (int)$_GET['ModuleID'];

    /* GET module details */
    $sqlM = "SELECT * FROM modules WHERE ModuleID = $moduleID";
    $resultM = mysqli_query($dbConn, $sqlM);

    if (!$resultM || mysqli_num_rows($resultM) == 0) {
        header("Location: student_modules.php");
        exit();
    }
    $module = mysqli_fetch_assoc($resultM);

    /* mark module as In Progress when first opened */
    $checkProgress = "SELECT ProgressID FROM progress 
                    WHERE UserID = $userID AND ModuleID = $moduleID";
    $resCheck = mysqli_query($dbConn, $checkProgress);

    if ($resCheck && mysqli_num_rows($resCheck) == 0) {
        // No progress record yet then mark as In Progress
        $sqlStart = "INSERT INTO progress 
                    (UserID, ModuleID, ProgressStatus, CompletionDate, ProgressPercentage)
                    VALUES ($userID, $moduleID, 'In Progress', NOW(), 0)";
        mysqli_query($dbConn, $sqlStart);
    }

    /* If user clicked done (either from modules.php or this page) */
    if (isset($_GET['mark']) && $_GET['mark'] == "done") {
        // Check if progress exists
        $check = "SELECT ProgressID FROM progress WHERE UserID = $userID AND ModuleID = $moduleID";
        $resC = mysqli_query($dbConn, $check);

        if ($resC && mysqli_num_rows($resC) > 0) {
            //Update existing progress
            $sqlu = "UPDATE progress 
                       SET ProgressStatus = 'Completed', ProgressPercentage = 100, CompletionDate = NOW()
                       WHERE UserID = $userID AND ModuleID = $moduleID";
            mysqli_query($dbConn, $sqlu);
        } else {
            // Insert new progress
            $sqli = "INSERT INTO progress (UserID, ModuleID, ProgressStatus,CompletionDate, ProgressPercentage)
                      VALUES ($userID, $moduleID, 'Completed', NOW(), 100)";
            mysqli_query($dbConn, $sqli);
        }

        // After done, redirect to avoid repeated update 
        header("Location: studentview_module.php?ModuleID=$moduleID");
        exit();
    }

    /* Get this student's current progress for display */
    $sqlP = "SELECT ProgressStatus, ProgressPercentage, CompletionDate
            FROM progress
            WHERE UserID = $userID AND ModuleID = $moduleID";
    $resP = mysqli_query($dbConn, $sqlP);
    $statusText = "Not Started";
    $percentText = "0%";
    $completionText = "-";

    if ($resP && mysqli_num_rows($resP) > 0) {
        $p = mysqli_fetch_assoc($resP);
        if (!empty($p['ProgressStatus'])) $statusText = $p['ProgressStatus'];
        if ($p['ProgressPercentage'] !== NULL) $percentText = $p['ProgressPercentage'] . "%";
        if (!empty($p['CompletionDate'])) $completionText = $p['CompletionDate'];
    }
    include "includes/header.php";
?>

<h2><?php echo htmlspecialchars($module['ModuleTitle']); ?></h2>

<p><b>Created By:</b> <?php echo htmlspecialchars($module['CreatedBy']); ?></p>
<p><b>Created Date:</b> <?php echo htmlspecialchars($module['CreatedDate']); ?></p>

<hr>

<div class="note-box">
    <?php echo nl2br(htmlspecialchars($module['ModuleDescription'])); ?>
</div>

<hr>

<p><b>Your Progress:</b> <?php echo htmlspecialchars($statusText); ?> (<?php echo htmlspecialchars($percentText); ?>)</p>
<p><b>Completion Date:</b> <?php echo htmlspecialchars($completionText); ?></p>

<?php if ($statusText != "Completed"): ?>
    <a class="btn btn-done" href="studentview_module.php?ModuleID=<?php echo $moduleID; ?>&mark=done">Mark as Done</a>
<?php else: ?>
    <span class="done-tag">Completed</span>
<?php endif; ?>

<br><br>
<a href="student_modules.php" class="back-btn"> Back to Modules</a>

<?php include "includes/footer.php"; ?>