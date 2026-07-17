<?php
include "conn.php";
$conn = $dbConn;

$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';

$userDateFilter = "";
if ($startDate && $endDate) {
    $userDateFilter = " AND DateJoined BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
}

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=user_status_report.csv");

$output = fopen("php://output", "w");
fputcsv($output, ["Status", "Total"]);

$q = mysqli_query($conn, "SELECT Status, COUNT(*) AS total FROM users WHERE 1 $userDateFilter GROUP BY Status");
while ($r = mysqli_fetch_assoc($q)) {
    fputcsv($output, $r);
}

fclose($output);
exit;




