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

    /* Student & Guest only view Approved content */
    $query = "Select * FROM content WHERE ApprovalStatus='Approve' ORDER BY UploadDate DESC";
    $result = mysqli_query($dbConn, $query);

?>

    <div class="main">
        <h1>Learning Content</h1>
        <p class="subtitle">Browse approved educational materials</p>
        <input type="text" id="searchInput" class="search-box" placeholder="Search by content title...">

        <div class="content-grid">
            <?php
                if (mysqli_num_rows($result) == 0) {
                    echo "<p>No content available at the moment.</p>";
                } 
            ?>

            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="content-card content-box"
                    data-title="<?= strtolower($row['ContentTitle']); ?>">

                     <h3><?= $row['ContentTitle']; ?></h3>
                    <p><strong>Type:</strong> <?= $row['ContentType']; ?></p>
                    <p class="content-desc" style="display:none;">
                        <?= $row['ContentDescription']; ?>
                    </p>

                    <button onclick="toggleDesc(this)" class="small-btn">
                        Show / Hide Description
                    </button>

                    <?php if (!empty($row['ContentFile'])): ?>
                        <p>
                             <a href="<?= $row['ContentFile']; ?>" target="_blank">
                                View Learning Content
                            </a>    
                        </p>

                    <?php endif; ?>

                    <small>
                        Uploaded by <?= $row['UploadedBy']; ?> |
                        <?= $row['UploadDate']; ?>
                    </small>
                </div>
            <?php endwhile; ?>
        </div>
        <br>
        <a href="student_dashboard.php" class="back-btn">← Back to Dashboard</a>    

    </div>
    
    <script src="studentview_content.js"></script>
     <?php
        include 'includes/footer.php';
    ?>


    


