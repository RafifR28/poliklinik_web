<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['pasien'])) {
    header("Location: login_pasien.php");
    exit();
}

require_once '../admin/koneksi.php';

$id_pasien = $_SESSION['id_pasien'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $no_rm = $_POST['no_rm'];
    $id_poli = $_POST['id_poli'];
    $id_jadwal = $_POST['id_jadwal'];
    $keluhan = $_POST['keluhan'];

    if (empty($no_rm) || empty($id_poli) || empty($id_jadwal) || empty($keluhan)) {
        $error = "Semua kolom harus diisi!";
    } else {
        $query_antrian = "SELECT COUNT(*) AS jumlah FROM daftar_poli WHERE id_jadwal = '$id_jadwal'";
        $result_antrian = $conn->query($query_antrian);
        $row_antrian = $result_antrian->fetch_assoc();
        $no_antrian = $row_antrian['jumlah'] + 1;

        $sql = $conn->prepare("INSERT INTO daftar_poli (id_pasien, id_jadwal, keluhan, no_antrian) VALUES (?, ?, ?, ?)");
        $sql->bind_param('iisi', $id_pasien, $id_jadwal, $keluhan, $no_antrian);
        if ($sql->execute()) {
            $success = "Pendaftaran berhasil!";
        } else {
            $error = "Terjadi kesalahan saat mendaftar.";
        }
    }
}

$query_riwayat = "SELECT dp.id, p.nama_poli, jp.hari, jp.jam_mulai, jp.jam_selesai, dp.keluhan, dp.no_antrian, d.nama AS nama_dokter,
                  (SELECT COUNT(*) FROM periksa pr WHERE pr.id_daftar_poli = dp.id) AS periksa_exists
                  FROM daftar_poli dp
                  JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
                  JOIN dokter d ON jp.id_dokter = d.id
                  JOIN poli p ON d.id_poli = p.id
                  WHERE dp.id_pasien = '$id_pasien'";
$result_riwayat = $conn->query($query_riwayat);

$query_jadwal = "SELECT jp.id, jp.hari, jp.jam_mulai, jp.jam_selesai, d.nama AS nama_dokter, d.id_poli
                 FROM jadwal_periksa jp
                 JOIN dokter d ON jp.id_dokter = d.id";
$result_jadwal = $conn->query($query_jadwal);

$jadwal = [];
while ($row_jadwal = $result_jadwal->fetch_assoc()) {
    $jadwal[] = $row_jadwal;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pasien</title>
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

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
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

        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .logout-btn {
            padding: 10px 15px;
            background-color: #1a3e6d;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            float: right;
            margin-bottom: 20px;
        }

        .logout-btn:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const jadwal = <?php echo json_encode($jadwal); ?>;
            const poliSelect = document.getElementById('id_poli');
            const jadwalSelect = document.getElementById('id_jadwal');

            poliSelect.addEventListener('change', function() {
                const selectedPoli = this.value;
                jadwalSelect.innerHTML = '<option value="">Pilih Jadwal</option>';

                jadwal.forEach(function(item) {
                    if (item.id_poli == selectedPoli) {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.textContent = `${item.hari} - ${item.jam_mulai} - ${item.jam_selesai} - ${item.nama_dokter}`;
                        jadwalSelect.appendChild(option);
                    }
                });
            });
        });
    </script>
</head>

<body>
    <div class="container">
        <h1>Dashboard Pasien</h1>
        <a href="logout.php" class="logout-btn">Logout</a>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?= $success; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?= $error; ?></div>
        <?php endif; ?>
        <form action="" method="post">
            <div class="form-group">
                <label for="no_rm">No RM:</label>
                <input type="text" id="no_rm" name="no_rm" value="<?= $_SESSION['no_rm']; ?>" readonly>
            </div>
            <div class="form-group">
                <label for="id_poli">Poli:</label>
                <select id="id_poli" name="id_poli" required>
                    <option value="">Pilih Poli</option>
                    <?php
                    $sql_poli = "SELECT * FROM poli";
                    $result_poli = $conn->query($sql_poli);
                    while ($row_poli = $result_poli->fetch_assoc()) {
                        echo "<option value='" . $row_poli['id'] . "'>" . $row_poli['nama_poli'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_jadwal">Jadwal:</label>
                <select id="id_jadwal" name="id_jadwal" required>
                    <option value="">Pilih Jadwal</option>
                </select>
            </div>
            <div class="form-group">
                <label for="keluhan">Keluhan:</label>
                <textarea id="keluhan" name="keluhan" required></textarea>
            </div>
            <button type="submit" class="btn">Daftar</button>
        </form>

        <h2>Riwayat Daftar Poli</h2>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Poli</th>
                    <th>Hari</th>
                    <th>Jam Mulai</th>
                    <th>Jam Selesai</th>
                    <th>Keluhan</th>
                    <th>No Antrian</th>
                    <th>Dokter</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_riwayat->num_rows > 0): ?>
                    <?php $no = 1; ?>
                    <?php while ($row = $result_riwayat->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['nama_poli']); ?></td>
                            <td><?= htmlspecialchars($row['hari']); ?></td>
                            <td><?= htmlspecialchars($row['jam_mulai']); ?></td>
                            <td><?= htmlspecialchars($row['jam_selesai']); ?></td>
                            <td><?= htmlspecialchars($row['keluhan']); ?></td>
                            <td><?= htmlspecialchars($row['no_antrian']); ?></td>
                            <td><?= htmlspecialchars($row['nama_dokter']); ?></td>
                            <td><?= $row['periksa_exists'] > 0 ? 'Sudah Diperiksa' : 'Belum Diperiksa'; ?></td>
                            <td>
                                <a href="riwayat_periksa.php?id=<?= $row['id']; ?>" class="btn">Riwayat</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10">Tidak ada riwayat daftar poli.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>