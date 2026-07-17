<?php
$pageTitle = "Badge Management"; 
include "conn.php";
$conn = $dbConn;
include "admin_layout_header.php";
include_once "system_alert.php";

/* ================= ADD BADGE ================= */
if (isset($_POST['add_badge'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $criteria = mysqli_real_escape_string($conn, $_POST['criteria']);
    $badgeImage = mysqli_real_escape_string($conn, $_POST['badge_image']);
    $competition = $_POST['competition_id'] ?: "NULL";

    mysqli_query($conn, "
        INSERT INTO badges (BadgeName, BadgeDescription, BadgeCriteria, CompetitionID, BadgeImage)
        VALUES ('$name', '$description', '$criteria', $competition, '$badgeImage')
    ");
}

/* ================= EDIT BADGE ================= */
if (isset($_POST['edit_badge'])) {
    $id = intval($_POST['badge_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $criteria = mysqli_real_escape_string($conn, $_POST['criteria']);
    $badgeImage = mysqli_real_escape_string($conn, $_POST['badge_image']);
    $competition = $_POST['competition_id'] ?: "NULL";

    mysqli_query($conn, "
        UPDATE badges SET
            BadgeName='$name',
            BadgeDescription='$description',
            BadgeCriteria='$criteria',
            CompetitionID=$competition,
            BadgeImage='$badgeImage'
        WHERE BadgeID=$id
    ");
}

/* ================= DELETE BADGE ================= */
if (isset($_POST['delete_badge'])) {
    $id = intval($_POST['badge_id']);
    mysqli_query($conn, "DELETE FROM badges WHERE BadgeID=$id");
}

/* ================= ASSIGN BADGE ================= */
if (isset($_POST['assign_badge'])) {
    $badgeId = intval($_POST['badge_id']);
    $userId  = intval($_POST['user_id']);

    // Get current timestamp in MySQL format
    $awardDate = date('Y-m-d H:i:s');

    mysqli_query($conn, "
        INSERT INTO badgesawarded (UserID, BadgeID, AwardDate)
        VALUES ($userId, $badgeId, '$awardDate')
    ");
}

/* ================= DATA ================= */
$search = $_GET['search'] ?? '';

$badges = mysqli_query($conn, "
    SELECT b.*, c.Title AS CompetitionTitle
    FROM badges b
    LEFT JOIN competition c ON b.CompetitionID = c.CompetitionID
    WHERE b.BadgeName LIKE '%" . mysqli_real_escape_string($conn, $search) . "%'
");

$competitions = mysqli_query(
    $conn,
    "SELECT CompetitionID, Title FROM competition ORDER BY Title ASC"
);

/* ONLY STUDENTS */
$students = mysqli_query(
    $conn,
    "SELECT UserID, Username FROM users WHERE Role='Student' ORDER BY Username ASC"
);
?>

<div class="main">

    <div class="page-header">
        <h1>Badge Management</h1>

        <form method="get" class="search-bar">
            <input type="text" name="search" placeholder="Search badge..." value="<?= htmlspecialchars($search) ?>">
        </form>

        <button class="btn" onclick="openModal('badgeModal')">+ Create Badge</button>
    </div>

    <div class="card-grid">
        <?php while ($b = mysqli_fetch_assoc($badges)) {

            $imgFile = $b['BadgeImage']
                ? "uploads/badges_image/" . $b['BadgeImage']
                : "uploads/badges_image/default.png";
        ?>

        <div class="badge-card">

            <!-- RESPONSIVE IMAGE -->
            <img src="<?= htmlspecialchars($imgFile) ?>"
                 alt="Badge Image"
                 style="width:100%;max-height:140px;object-fit:contain;margin-bottom:10px;">

            <h3><?= htmlspecialchars($b['BadgeName']) ?></h3>

            <p><strong>Description</strong><br>
                <?= nl2br(htmlspecialchars($b['BadgeDescription'])) ?>
            </p>

            <p><strong>Criteria</strong><br>
                <?= nl2br(htmlspecialchars($b['BadgeCriteria'])) ?>
            </p>

            <?php if ($b['CompetitionTitle']) { ?>
                <p><strong>Competition:</strong> <?= htmlspecialchars($b['CompetitionTitle']) ?></p>
            <?php } ?>

            <div class="card-actions">
                <button class="btn small"
                    onclick="editBadge(
                        <?= $b['BadgeID'] ?>,
                        '<?= addslashes($b['BadgeName']) ?>',
                        '<?= addslashes($b['BadgeDescription']) ?>',
                        '<?= addslashes($b['BadgeCriteria']) ?>',
                        '<?= addslashes($b['BadgeImage']) ?>',
                        '<?= $b['CompetitionID'] ?>'
                    )">Edit</button>

                <button class="btn small accent"
                    onclick="openAssignModal(<?= $b['BadgeID'] ?>)">
                    Assign
                </button>

                <form method="post" style="display:inline">
                    <input type="hidden" name="badge_id" value="<?= $b['BadgeID'] ?>">
                    <button class="btn danger small" name="delete_badge"
                        onclick="return confirm('Delete this badge?')">
                        Delete
                    </button>
                </form>
            </div>
        </div>

        <?php } ?>
    </div>
</div>

<!-- ================= CREATE / EDIT MODAL ================= -->
<div id="badgeModal" class="modal">
    <div class="modal-content">
        <h3 id="modalTitle">Create Badge</h3>

        <form method="post">
            <input type="hidden" name="badge_id" id="badge_id">

            <label>Badge Name</label>
            <input type="text" name="name" id="name" required>

            <label>Description</label>
            <textarea name="description" id="description" required></textarea>

            <label>Criteria</label>
            <textarea name="criteria" id="criteria" required></textarea>

            <label>Competition</label>
            <select name="competition_id" id="competition_id">
                <option value="">-- None --</option>
                <?php while ($c = mysqli_fetch_assoc($competitions)) { ?>
                    <option value="<?= $c['CompetitionID'] ?>">
                        <?= htmlspecialchars($c['Title']) ?>
                    </option>
                <?php } ?>
            </select>

            <label>Badge Image Filename</label>
            <input type="text" name="badge_image" id="badge_image">

            <button class="btn" id="submitBtn" name="add_badge">Create</button>
            <button type="button" class="btn danger" onclick="closeModal('badgeModal')">Cancel</button>
        </form>
    </div>
</div>

<!-- ================= ASSIGN MODAL ================= -->
<div id="assignModal" class="modal">
    <div class="modal-content">
        <h3>Assign Badge to Student</h3>

        <form method="post">
            <input type="hidden" name="badge_id" id="assign_badge_id">

            <label>Select Student</label>
            <select name="user_id" required>
                <?php while ($s = mysqli_fetch_assoc($students)) { ?>
                    <option value="<?= $s['UserID'] ?>">
                        <?= htmlspecialchars($s['Username']) ?>
                    </option>
                <?php } ?>
            </select>

            <button class="btn accent" name="assign_badge">Assign</button>
            <button type="button" class="btn danger" onclick="closeModal('assignModal')">Cancel</button>
        </form>
    </div>
</div>

<script>
function openAssignModal(badgeId) {
    document.getElementById('assign_badge_id').value = badgeId;
    document.getElementById('assignModal').style.display = 'flex';
}

function editBadge(id, name, desc, criteria, image, competition) {
    openModal('badgeModal');
    document.getElementById('modalTitle').innerText = "Edit Badge";
    document.getElementById('badge_id').value = id;
    document.getElementById('name').value = name;
    document.getElementById('description').value = desc;
    document.getElementById('criteria').value = criteria;
    document.getElementById('badge_image').value = image;
    document.getElementById('competition_id').value = competition;
    document.getElementById('submitBtn').name = "edit_badge";
    document.getElementById('submitBtn').innerText = "Save Changes";
}
</script>

<?php include "admin_layout_footer.php"; ?>
