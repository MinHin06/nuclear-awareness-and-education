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

?>

    <div class="welcome-wrap">
        <h2 class="welcome">Welcome back, <?php echo $username; ?></h2>
    </div>


    <div class="quick">
        <div class="btn-grid">
            <a href="student_activities.php" class="navbtn">Activities</a>
            <a href="student_modules.php" class="navbtn">Modules</a>
            <a href="student_discussion.php" class="navbtn">Discussion</a>
            <a href="student_view_leaderboard.php" class="navbtn">Leaderboards</a>
            <a href="student_points_badges.php" class="navbtn">Points & Badges</a>
            <a href="studentview_content.php" class="navbtn">Content</a>
        </div>
    </div>
    
    <div class ="frame">
        <div class="section-card">
    
        <h3>Announcements</h3>

        <?php
        //Latest module
        $sql = "Select * FROM modules ORDER BY  CreatedDate DESC LIMIT 1";
        $result = mysqli_query($dbConn, $sql);

        if(mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_assoc($result);
            echo "<p><b>New Module:</b> " . $row['ModuleTitle'] . "</p>";
        } else {
            echo "<p>No module available.</p>";
        }


        //Latest activity
        $sql = "Select * FROM competition where ApprovalStatus ='Approved' ORDER BY CreatedDate DESC Limit 1";
        $result = mysqli_query($dbConn, $sql);

        if(mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_assoc($result);
            echo "<p><b>New Activity:</b> " . $row['CompetitionType'] . " - " . $row['Title'] . "</p>";
        } else {
            echo "<p>No activity available.</p>";
        }
        ?>
        </div>

        <div class="section-card">

        <h3>Module Progression</h3>
        <?php
        //complete count
        $sql="SELECT * FROM progress WHERE UserID=$userID AND ProgressStatus = 'Completed'";
        $result = mysqli_query($dbConn, $sql);
        $completedCount = mysqli_num_rows($result);

        //In Progress count
        $sql="SELECT * FROM progress WHERE UserID=$userID AND ProgressStatus = 'In Progress'";
        $result = mysqli_query($dbConn, $sql);
        $inProgressCount = mysqli_num_rows($result);

        //AVG %
        $sql = "SELECT AVG(ProgressPercentage) AS avgProgress FROM progress WHERE UserID=$userID";
        $result = mysqli_query($dbConn, $sql);
        $row = mysqli_fetch_assoc($result);
        $avgProgress = (int)$row['avgProgress'];

        echo "<p>Modules Completed: " . $completedCount . "</p>";
        echo "<p>Modules In Progress: " . $inProgressCount . "</p>";
        echo "<p>Average Progress: " . $avgProgress . "%</p>";
        ?>

        </div>

        <div class="section-card">

        <h3>Reminder / To-do</h3>

        <b>Upcoming events</b>
        <?php
        $sql = "SELECT * FROM competition WHERE ApprovalStatus='Pending' AND StartDate > CURDATE() ORDER BY StartDate ASC LIMIT 2";
        
        $result = mysqli_query($dbConn, $sql);
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_assoc($result)){
                echo "<p>- " . $row['Title'] . " (Starts: " . $row['StartDate'] . ")</p>";
            }
        } else {
            echo "<p>No upcoming events.</p>";
        }
        ?>

        <br>

        <b>Continue Modules</b>
        <?php
        $sql = "SELECT modules.ModuleTitle, progress.ProgressPercentage
                FROM progress
                JOIN modules ON progress.ModuleID = modules.ModuleID
                WHERE progress.UserID=$userID
                AND progress.ProgressStatus != 'Completed'
                LIMIT 2";
        $result = mysqli_query($dbConn, $sql);

        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_assoc($result)){
                echo "<p>- " . $row['ModuleTitle'] . " (" . $row['ProgressPercentage'] . "% )</p>";
            }
        } else {
            echo "<p>No pending modules.</p>";
        }
        ?>

        </div>

    </div>

    <?php
    include 'includes/footer.php';
    ?>

    
    
