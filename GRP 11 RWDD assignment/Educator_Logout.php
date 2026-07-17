<?php
session_start();

/* Clear session data */
$_SESSION = [];

/* Destroy session */
session_destroy();

/* Redirect to login */
header("Location: login.html");
exit;
?>
