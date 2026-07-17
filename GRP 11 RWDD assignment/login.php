<?php
header('Content-Type: text/plain');
session_start();
include "conn.php"; // ensure this file does not echo anything

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($username === '' || $password === '') {
  echo "ERROR";
  exit;
}

$sql = "SELECT UserID, Username, Email, Password, Role
        FROM users
        WHERE Username = ? OR Email = ?
        LIMIT 1";
$stmt = mysqli_prepare($dbConn, $sql);
if (!$stmt) { echo "ERROR"; exit; }

mysqli_stmt_bind_param($stmt, 'ss', $username, $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
  $stored = $row['Password'];

  // DEMO ONLY: direct plain-text comparison
  $valid = ($stored === $password);

  if ($valid) {
    // Store session data
    $_SESSION['UserID'] = $row['UserID'];
    $_SESSION['Username'] = $row['Username'];
    $_SESSION['Role'] = $row['Role'];
    $_SESSION['Email'] = $row['Email'];
    
    // Return the role so the frontend can route appropriately
    echo $row['Role']; // e.g., "Admin", "Educator", "Student", "Guest"
    exit;
  }
}

echo "ERROR";
exit;