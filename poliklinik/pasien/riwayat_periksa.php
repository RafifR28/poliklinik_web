<?php
session_start();
require_once '../admin/koneksi.php';

if (!isset($_SESSION['pasien'])) {
    header("Location: login_pasien.php");
    exit();
}

$id_daftar_poli = $_GET['id'];

$query = "SELECT dp.id, p.nama_poli, jp.hari, jp.jam_mulai, jp.jam_selesai, dp.no_antrian, d.nama AS nama_dokter, pr.tgl_periksa, pr.catatan
          FROM daftar_poli dp
          JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
          JOIN dokter d ON jp.id_dokter = d.id
          JOIN poli p ON d.id_poli = p.id
          LEFT JOIN periksa pr ON dp.id = pr.id_daftar_poli
          WHERE dp.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_daftar_poli);
$stmt->execute();
$result = $stmt->get_result();
$riwayat = $result->fetch_assoc();

$query_obat = "SELECT o.nama_obat, o.kemasan, o.harga
               FROM detail_periksa dp
               JOIN obat o ON dp.id_obat = o.id
               WHERE dp.id_periksa = (SELECT id FROM periksa WHERE id_daftar_poli = ? LIMIT 1)";
$stmt_obat = $conn->prepare($query_obat);
$stmt_obat->bind_param("i", $id_daftar_poli);
$stmt_obat->execute();
$result_obat = $stmt_obat->get_result();
$obat = [];
while ($row_obat = $result_obat->fetch_assoc()) {
    $obat[] = $row_obat;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Periksa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1, h2 {
            text-align: center;
            color: #333;
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

        .btn {
            padding: 10px 15px;
            background-color: #1a3e6d;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            width: 100%;
            text-align: center;
            margin-top: 10px;
        }

        .btn:hover {
            background-color: #2a5d99;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Riwayat Periksa</h1>
        <table>
            <thead>
                <tr>
                    <th>Nama Poli</th>
                    <th>Nama Dokter</th>
                    <th>Hari</th>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th>Antrian</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= htmlspecialchars($riwayat['nama_poli']); ?></td>
                    <td><?= htmlspecialchars($riwayat['nama_dokter']); ?></td>
                    <td><?= htmlspecialchars($riwayat['hari']); ?></td>
                    <td><?= htmlspecialchars($riwayat['jam_mulai']); ?></td>
                    <td><?= htmlspecialchars($riwayat['jam_selesai']); ?></td>
                    <td><?= htmlspecialchars($riwayat['no_antrian']); ?></td>
                </tr>
            </tbody>
        </table>

        <h2>Informasi Periksa</h2>
        <table>
            <thead>
                <tr>
                    <th>Tanggal Periksa</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= htmlspecialchars($riwayat['tgl_periksa']); ?></td>
                    <td><?= htmlspecialchars($riwayat['catatan']); ?></td>
                </tr>
            </tbody>
        </table>

        <h2>Daftar Obat</h2>
        <table>
            <thead>
                <tr>
                    <th>Nama Obat</th>
                    <th>Kemasan</th>
                    <th>Harga</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($obat as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nama_obat']); ?></td>
                        <td><?= htmlspecialchars($item['kemasan']); ?></td>
                        <td><?= htmlspecialchars($item['harga']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Biaya</h2>
        <p>Jasa Periksa Dokter: Rp 150.000</p>
        <p>Total Biaya Obat: Rp <?= array_sum(array_column($obat, 'harga')); ?></p>
        <p>Total Biaya: Rp <?= 150000 + array_sum(array_column($obat, 'harga')); ?></p>

        <a href="dashboard_pasien.php" class="btn">Kembali</a>
    </div>
</body>
</html>