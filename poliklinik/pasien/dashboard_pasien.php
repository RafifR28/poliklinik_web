<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['pasien'])) {
    header("Location: login_pasien.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Pasien</title>
</head>
<body>
    <h1>Selamat datang, <?= $_SESSION['pasien']; ?>!</h1>
    <a href="logout.php">Logout</a>
</body>
</html>
