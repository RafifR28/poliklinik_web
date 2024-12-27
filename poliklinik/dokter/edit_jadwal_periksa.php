<?php
session_start();
require_once '../admin/koneksi.php';

if (!isset($_SESSION['dokter']['id'])) {
    header("Location: login_dokter.php");
    exit();
}

$id_dokter = $_SESSION['dokter']['id'];
$id_jadwal = $_GET['id'];
$error = '';
$success = '';

$sql = "SELECT hari, jam_mulai, jam_selesai, status FROM jadwal_periksa WHERE id = ? AND id_dokter = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ii", $id_jadwal, $id_dokter);
    $stmt->execute();
    $stmt->bind_result($hari, $jam_mulai, $jam_selesai, $status);
    $stmt->fetch();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hari = $_POST["hari"];
    $jam_mulai = $_POST["jam_mulai"];
    $jam_selesai = $_POST["jam_selesai"];
    $status = $_POST["status"];

    if (empty($hari) || empty($jam_mulai) || empty($jam_selesai)) {
        $error = "Semua kolom harus diisi!";
    } else {
        $sql = "UPDATE jadwal_periksa SET hari = ?, jam_mulai = ?, jam_selesai = ?, status = ? WHERE id = ? AND id_dokter = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssiii", $hari, $jam_mulai, $jam_selesai, $status, $id_jadwal, $id_dokter);
            if ($stmt->execute()) {
                $success = "Jadwal berhasil diperbarui!";
                header("Location: dashboard_dokter.php");
                exit();
            } else {
                $error = "Terjadi kesalahan saat memperbarui jadwal.";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Jadwal Periksa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        h1 {
            margin-bottom: 20px;
            font-size: 24px;
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

        input, select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Jadwal Periksa</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form action="edit_jadwal_periksa.php?id=<?= $id_jadwal; ?>" method="POST">
            <div class="form-group">
                <label for="hari">Hari</label>
                <select name="hari" id="hari" required>
                    <option value="">-- Pilih Hari --</option>
                    <option value="Senin" <?= $hari == 'Senin' ? 'selected' : ''; ?>>Senin</option>
                    <option value="Selasa" <?= $hari == 'Selasa' ? 'selected' : ''; ?>>Selasa</option>
                    <option value="Rabu" <?= $hari == 'Rabu' ? 'selected' : ''; ?>>Rabu</option>
                    <option value="Kamis" <?= $hari == 'Kamis' ? 'selected' : ''; ?>>Kamis</option>
                    <option value="Jumat" <?= $hari == 'Jumat' ? 'selected' : ''; ?>>Jumat</option>
                    <option value="Sabtu" <?= $hari == 'Sabtu' ? 'selected' : ''; ?>>Sabtu</option>
                    <option value="Minggu" <?= $hari == 'Minggu' ? 'selected' : ''; ?>>Minggu</option>
                </select>
            </div>
            <div class="form-group">
                <label for="jam_mulai">Jam Mulai</label>
                <input type="time" id="jam_mulai" name="jam_mulai" value="<?= htmlspecialchars($jam_mulai); ?>" required>
            </div>
            <div class="form-group">
                <label for="jam_selesai">Jam Selesai</label>
                <input type="time" id="jam_selesai" name="jam_selesai" value="<?= htmlspecialchars($jam_selesai); ?>" required>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" required>
                    <option value="1" <?= $status == 1 ? 'selected' : ''; ?>>Aktif</option>
                    <option value="0" <?= $status == 0 ? 'selected' : ''; ?>>Tidak Aktif</option>
                </select>
            </div>
            <button type="submit" class="btn">Simpan</button>
            <a href="dashboard_dokter.php" class="btn">Kembali</a>
        </form>
    </div>
</body>
</html>