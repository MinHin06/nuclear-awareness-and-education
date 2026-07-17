<?php
header('Content-Type: text/plain');
require_once __DIR__ . '/conn.php'; // conn.php must be in this folder OR fix path

$first    = trim($_POST['first_name']  ?? '');
$last     = trim($_POST['last_name']   ?? '');
$username = trim($_POST['username']    ?? '');
$email    = trim($_POST['email']       ?? '');
$password =        $_POST['password']  ?? '';
$dob      =        $_POST['dob']       ?? '';
$bio      = trim($_POST['bio']         ?? '');

if ($first === '' || $last === '' || $username === '' || $email === '' || $password === '' || $dob === '') {
  echo "ERROR: Please fill in all required fields.";
  exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  echo "ERROR: Invalid email format.";
  exit;
}
if (strlen($password) < 6) {
  echo "ERROR: Password must be at least 6 characters.";
  exit;
}

// Force role to Student
$role = 'Student';

// DEMO ONLY: store password as plain text (INSECURE — do not use in production)
$plainPassword = $password;

// Check existing username/email (both are UNIQUE in your table)
$checkSql = "SELECT 1 FROM users WHERE Email = ? OR Username = ? LIMIT 1";
$check = mysqli_prepare($dbConn, $checkSql);
mysqli_stmt_bind_param($check, 'ss', $email, $username);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);
if (mysqli_stmt_num_rows($check) > 0) {
  echo "ERROR: Email or Username already exists.";
  exit;
}
mysqli_stmt_close($check);

// Insert row (per your schema)
$sql = "INSERT INTO users
  (Email, Username, Password, Role, FirstName, LastName, DateOfBirth, DateJoined, Bio, Status)
  VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?, 'Active')";
$stmt = mysqli_prepare($dbConn, $sql);
if (!$stmt) {
  echo "ERROR: Unable to prepare statement.";
  exit;
}
mysqli_stmt_bind_param(
  $stmt,
  'ssssssss',
  $email,
  $username,
  $plainPassword, // plain text password
  $role,
  $first,
  $last,
  $dob,
  $bio
);

if (!mysqli_stmt_execute($stmt)) {
  echo "ERROR: Unable to create account.";
  exit;
}
echo "OK";
exit;