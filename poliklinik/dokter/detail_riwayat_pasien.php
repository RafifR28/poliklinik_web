<?php
session_start();
require_once '../admin/koneksi.php';

if (!isset($_SESSION['dokter']['id'])) {
    header("Location: login_dokter.php");
    exit();
}

$id_pasien = $_GET['id'];

$sql = "SELECT dp.id, pr.tgl_periksa, dp.keluhan, dp.no_antrian, po.nama_poli, d.nama AS nama_dokter
        FROM daftar_poli dp
        JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
        JOIN dokter d ON jp.id_dokter = d.id
        JOIN poli po ON d.id_poli = po.id
        JOIN periksa pr ON dp.id = pr.id_daftar_poli
        WHERE dp.id_pasien = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_pasien);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Riwayat Pasien</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
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

        .btn {
            padding: 10px 15px;
            background-color: #1a3e6d;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #2a5d99;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <img src="../assets/img/hospital.svg" alt="Hospital Logo" width="50px">
        <h2>Panel Dokter</h2>
        <a href="dashboard_dokter.php">Jadwal Periksa</a>
        <a href="daftar_periksa_pasien.php">Memeriksa Pasien</a>
        <a href="riwayat_pasien.php" class="active">Riwayat Pasien</a>
        <a href="profil_dokter.php">Profil</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Detail Riwayat Pasien</h1>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal Periksa</th>
                    <th>Keluhan</th>
                    <th>No Antrian</th>
                    <th>Poli</th>
                    <th>Dokter</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php $no = 1; ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['tgl_periksa']); ?></td>
                            <td><?= htmlspecialchars($row['keluhan']); ?></td>
                            <td><?= htmlspecialchars($row['no_antrian']); ?></td>
                            <td><?= htmlspecialchars($row['nama_poli']); ?></td>
                            <td><?= htmlspecialchars($row['nama_dokter']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">Tidak ada data riwayat periksa pasien.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="riwayat_pasien.php" class="btn">Kembali</a>
    </div>
</body>
</html>