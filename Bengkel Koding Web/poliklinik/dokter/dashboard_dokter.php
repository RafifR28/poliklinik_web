<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['dokter'])) {
    header("Location: login_dokter.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Pasien</title>
</head>
<body>
    <h1>Selamat datang, <?= $_SESSION['dokter']; ?>!</h1>
    <a href="logout.php">Logout</a>
</body>
</html>
