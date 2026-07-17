<?php
$pageTitle = "Content Approval"; 
include "conn.php";
$conn = $dbConn;
include "admin_layout_header.php";
include_once "system_alert.php";

if (isset($_POST['update_content'])) {
    $id = $_POST['content_id'];
    $status = $_POST['status'];

    mysqli_query($conn, "
        UPDATE content
        SET ApprovalStatus='$status'
        WHERE ContentID=$id
    ");
}

if (isset($_POST['delete_content'])) {
    $id = $_POST['content_id'];
    mysqli_query($conn, "DELETE FROM content WHERE ContentID=$id");
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

$sql = "
SELECT 
    ContentID,
    ContentTitle,
    ContentType,
    ContentFile,
    ApprovalStatus,
    UploadDate,
    UploadedBy,
    ContentDescription
FROM content
WHERE ContentTitle LIKE '%$search%'
";

if ($statusFilter != '') {
    $sql .= " AND ApprovalStatus='$statusFilter'";
}

$sql .= " ORDER BY UploadDate DESC";

$contents = mysqli_query($conn, $sql);
?>

<div class="page-header">
    <h1>Content Management</h1>

    <form method="get" class="search-bar">
        <input type="text" name="search" placeholder="Search title..." value="<?= htmlspecialchars($search) ?>">
    </form>
</div>

<form method="get" class="filter-bar">
    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">

    <label>Filter by Status:</label>
    <select name="status" onchange="this.form.submit()">
        <option value="">All</option>
        <option value="Approve" <?= $statusFilter == 'Approve' ? 'selected' : '' ?>>Approve</option>
        <option value="Reject" <?= $statusFilter == 'Reject' ? 'selected' : '' ?>>Reject</option>
        <option value="Pending" <?= $statusFilter == 'Pending' ? 'selected' : '' ?>>Pending</option>

    </select>
</form>

<table class="data-table">
    <tr>
        <th>Title</th>
        <th>Educator (UploadedBy)</th>
        <th>Resource Type</th>
        <th>Submission Date</th>
        <th>Status</th>
        <th>Preview</th>
        <th>Action</th>
    </tr>

    <?php while ($c = mysqli_fetch_assoc($contents)) { ?>
        <tr>
            <td><?= htmlspecialchars($c['ContentTitle']) ?></td>
            <td><?= htmlspecialchars($c['UploadedBy']) ?></td>
            <td><?= htmlspecialchars($c['ContentType']) ?></td>
            <td><?= date('d M Y', strtotime($c['UploadDate'])) ?></td>
            <td><?= htmlspecialchars($c['ApprovalStatus']) ?></td>

            <td>
                <button class="btn small accent" onclick="openContentViewModal(
                '<?= addslashes(htmlspecialchars($c['ContentTitle'])) ?>',
                '<?= addslashes(htmlspecialchars($c['ContentDescription'])) ?>'
            )">
                    View
                </button>
            </td>

            <td>
                <button type="button" class="btn small" onclick="openContentEditModal(
                <?= $c['ContentID'] ?>,
                '<?= htmlspecialchars($c['ApprovalStatus']) ?>'
            )">
                    Edit
                </button>

                <form method="post" style="display:inline">
                    <input type="hidden" name="content_id" value="<?= $c['ContentID'] ?>">
                    <button name="delete_content" class="btn danger small" onclick="return confirm('Delete this content?')">
                        Delete
                    </button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>

<div id="editContentModal" class="modal">
    <div class="modal-content">
        <h3>Update Content Status</h3>

        <form method="post">
            <input type="hidden" name="content_id" id="editContentId">

            <label>Status</label>
            <select name="status" id="editContentStatus">
                <option>Approve</option>
                <option>Reject</option>
                <option>Pending</option>
            </select>

            <button name="update_content" class="btn">Save</button>
            <button type="button" onclick="closeModal('editContentModal')" class="btn danger">
                Cancel
            </button>
        </form>
    </div>
</div>

<div id="viewContentModal" class="modal">
    <div class="modal-content">
        <h3 id="viewContentTitle"></h3>
        <p id="viewContentDescription"></p>
        <button type="button" onclick="closeModal('viewContentModal')" class="btn danger">Close</button>
    </div>
</div>

<script>
    function openContentEditModal(id, status) {
        document.getElementById('editContentId').value = id;
        document.getElementById('editContentStatus').value = status;
        document.getElementById('editContentModal').style.display = 'flex';
    }

    function openContentViewModal(title, description) {
        document.getElementById('viewContentTitle').innerText = title;
        document.getElementById('viewContentDescription').innerText = description;
        document.getElementById('viewContentModal').style.display = 'flex';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }
</script>

<?php include "admin_layout_footer.php"; ?>