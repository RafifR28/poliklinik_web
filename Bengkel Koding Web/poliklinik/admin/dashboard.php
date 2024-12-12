<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");
if (!isset($_SESSION['admin'])) {
    header("Location: login_admin.php");
    exit();
}

require_once 'koneksi.php';

$sql_poli = "SELECT COUNT(*) as count FROM poli";
$result_poli = $conn->query($sql_poli);
$row_poli = $result_poli->fetch_assoc();
$count_poli = $row_poli['count'];

$sql_dokter = "SELECT COUNT(*) as count FROM dokter";
$result_dokter = $conn->query($sql_dokter);
$row_dokter = $result_dokter->fetch_assoc();
$count_dokter = $row_dokter['count'];

$sql_pasien = "SELECT COUNT(*) as count FROM pasien";
$result_pasien = $conn->query($sql_pasien);
$row_pasien = $result_pasien->fetch_assoc();
$count_pasien = $row_pasien['count'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
        }

        .sidebar {
            width: 220px;
            height: 100vh;
            background-color: #1a3e6d;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 20px;
        }

        .sidebar img {
            margin-bottom: 20px;
        }

        .sidebar h2 {
            margin: 0 0 20px 0;
            font-size: 20px;
            text-align: center;
        }

        .sidebar a {
            text-decoration: none;
            color: white;
            display: block;
            width: 100%;
            text-align: center;
            padding: 10px 0;
            margin-bottom: 10px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #2a5d99;
            color: #e0e0e0;
        }

        .sidebar a.active {
            background-color: #144266;
            color: #ffffff;
            font-weight: bold;
            border-left: 5px solid #ffffff;
        }

        .content {
            margin-left: 240px;
            padding: 20px;
            width: calc(100% - 240px);
            background-color: #f4f4f4;
            min-height: 100vh;
        }

        .dashboard-container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
        }

        .dashboard-item {
            background-color: white;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 30%;
            margin-bottom: 20px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .dashboard-item:hover {
            transform: translateY(-5px);
        }

        .dashboard-item h3 {
            margin-top: 0;
            font-size: 24px;
            color: #333;
        }

        .dashboard-item p {
            font-size: 18px;
            color: #666;
        }

        .dashboard-item img {
            width: 50px;
            height: 50px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <img src="../assets/img/hospital.svg" alt="Hospital Logo" width="50px">
        <h2>Admin Panel Poliklinik</h2>
        <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>
        <a href="kelola_dokter.php" class="<?= basename($_SERVER['PHP_SELF']) === 'kelola_dokter.php' ? 'active' : '' ?>">Mengelola Dokter</a>
        <a href="kelola_pasien.php" class="<?= basename($_SERVER['PHP_SELF']) === 'kelola_pasien.php' ? 'active' : '' ?>">Mengelola Pasien</a>
        <a href="kelola_poli.php" class="<?= basename($_SERVER['PHP_SELF']) === 'kelola_poli.php' ? 'active' : '' ?>">Mengelola Poli</a>
        <a href="kelola_obat.php" class="<?= basename($_SERVER['PHP_SELF']) === 'kelola_obat.php' ? 'active' : '' ?>">Mengelola Obat</a>
        <a class="logout" href="logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Halaman <?= $_SESSION['admin']; ?>!</h1>
        <p>Dashboard Admin</p>

        <div class="dashboard-container">
            <div class="dashboard-item">
                <img src="../assets/img/poli.svg" alt="Poli Icon">
                <h3>Jumlah Poli</h3>
                <p><?= $count_poli; ?></p>
            </div>
            <div class="dashboard-item">
                <img src="../assets/img/doctor-logo.svg" alt="Dokter Icon">
                <h3>Jumlah Dokter</h3>
                <p><?= $count_dokter; ?></p>
            </div>
            <div class="dashboard-item">
                <img src="../assets/img/human.svg" alt="Pasien Icon">
                <h3>Jumlah Pasien</h3>
                <p><?= $count_pasien; ?></p>
            </div>
        </div>
    </div>
</body>
</html>
