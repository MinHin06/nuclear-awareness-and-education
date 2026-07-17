<?php
$pageTitle = "User Management"; 
include "conn.php";
$conn = $dbConn;
include "admin_layout_header.php";
include_once "system_alert.php";

if (isset($_POST['add_user'])) {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $dob = $_POST['dateofbirth'];
    $bio = $_POST['bio'];
    $status = $_POST['status'];

    mysqli_query($conn, "
        INSERT INTO users 
        (Email, Username, Password, Role, FirstName, LastName, DateOfBirth, DateJoined, Bio, Status)
        VALUES 
        ('$email', '$username', '$password', '$role',
        '$firstname', '$lastname', '$dob', NOW(), '$bio', '$status')
    ");
}

if (isset($_POST['edit_user'])) {
    $id = $_POST['user_id'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    mysqli_query($conn, "
        UPDATE users 
        SET Role='$role', Status='$status'
        WHERE UserID=$id
    ");
}

if (isset($_POST['delete_user'])) {
    $id = $_POST['user_id'];
    mysqli_query($conn, "DELETE FROM users WHERE UserID=$id");
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$roleFilter = isset($_GET['role']) ? $_GET['role'] : '';

$sql = "
SELECT 
    users.UserID,
    users.Username,
    users.Role,
    users.Status,
    users.DateJoined,
    COUNT(DISTINCT progress.ModuleID) AS ModulesTaken,
    COUNT(DISTINCT badgesawarded.BadgeID) AS BadgesEarned
FROM users
LEFT JOIN progress ON users.UserID = progress.UserID
LEFT JOIN badgesawarded ON users.UserID = badgesawarded.UserID
WHERE users.Username LIKE '%$search%'
";

if ($roleFilter != '') {
    $sql .= " AND users.Role = '$roleFilter'";
}

$sql .= "
GROUP BY users.UserID
ORDER BY users.Username ASC
";

$users = mysqli_query($conn, $sql);
?>

<div class="page-header">
    <h1>Manage Users</h1>

    <form method="get" class="search-bar">
        <input type="text" name="search" placeholder="Search username..." value="<?= htmlspecialchars($search) ?>">
    </form>

    <button type="button" class="btn" onclick="openAddModal()">
        + Add User
    </button>
</div>

<form method="get" class="filter-bar">
    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">

    <label>Sort by Role:</label>
    <select name="role" onchange="this.form.submit()">
        <option value="">All</option>
        <option value="Admin" <?= $roleFilter == 'Admin' ? 'selected' : '' ?>>Admin</option>
        <option value="Educator" <?= $roleFilter == 'Educator' ? 'selected' : '' ?>>Educator</option>
        <option value="Student" <?= $roleFilter == 'Student' ? 'selected' : '' ?>>Student</option>
    </select>
</form>

<table class="data-table">
    <tr>
        <th>Username</th>
        <th>Role</th>
        <th>Registered</th>
        <th>Status</th>
        <th>Modules</th>
        <th>Badges</th>
        <th>Action</th>
    </tr>

    <?php while ($u = mysqli_fetch_assoc($users)) { ?>
        <tr>
            <td><?= $u['Username'] ?></td>
            <td><?= $u['Role'] ?></td>
            <td><?= date('d M Y', strtotime($u['DateJoined'])) ?></td>
            <td><?= $u['Status'] ?></td>
            <td><?= $u['ModulesTaken'] ?></td>
            <td><?= $u['BadgesEarned'] ?></td>
            <td>
                <button type="button" class="btn small" onclick="openEditModal(
                <?= $u['UserID'] ?>,
                '<?= $u['Role'] ?>',
                '<?= $u['Status'] ?>'
            )">Edit</button>

                <form method="post" style="display:inline">
                    <input type="hidden" name="user_id" value="<?= $u['UserID'] ?>">
                    <button name="delete_user" class="btn danger small"
                        onclick="return confirm('Delete this user?')">Delete</button>
                </form>
            </td>
        </tr>
    <?php } ?>
</table>

<div id="addModal" class="modal">
    <div class="modal-content">
        <h3>Add User</h3>

        <form method="post">
            <input type="email" name="email" placeholder="Email" required>
            <input name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input name="firstname" placeholder="First Name" required>
            <input name="lastname" placeholder="Last Name" required>

            <label>Date of Birth</label>
            <input type="date" name="dateofbirth" required>

            <textarea name="bio" placeholder="Short Bio" rows="3"></textarea>

            <select name="role">
                <option>Student</option>
                <option>Educator</option>
                <option>Admin</option>
            </select>

            <select name="status">
                <option>Active</option>
                <option>Inactive</option>
            </select>

            <button name="add_user" class="btn">Add</button>
            <button type="button" onclick="closeModal('addModal')" class="btn danger">Cancel</button>
        </form>
    </div>
</div>

<div id="editModal" class="modal">
    <div class="modal-content">
        <h3>Edit User</h3>

        <form method="post">
            <input type="hidden" name="user_id" id="editUserId">

            <label>Role</label>
            <select name="role" id="editRole">
                <option>Student</option>
                <option>Educator</option>
                <option>Admin</option>
            </select>

            <label>Status</label>
            <select name="status" id="editStatus">
                <option>Active</option>
                <option>Inactive</option>
            </select>

            <button name="edit_user" class="btn">Save</button>
            <button type="button" onclick="closeModal('editModal')" class="btn danger">Cancel</button>
        </form>
    </div>
</div>


<?php include "admin_layout_footer.php"; ?>