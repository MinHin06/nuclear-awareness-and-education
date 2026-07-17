<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : "Admin Panel" ?></title>
    <?php if (isset($pageDescription)) { ?>
        <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
    <?php } ?>
    
    <!-- your existing CSS/JS includes -->
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
</head>

<body class="<?= (isset($_SESSION['theme']) && $_SESSION['theme'] === 'dark') ? 'dark' : '' ?>">

    <?php include "topbar.php"; ?>
    <?php include "admin_sidebar.php"; ?>

    <div class="main">