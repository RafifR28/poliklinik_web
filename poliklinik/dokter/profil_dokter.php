<?php
session_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['dokter']) || !is_array($_SESSION['dokter'])) {
    die('Session tidak valid. Silakan login kembali.');
}

require_once '../admin/koneksi.php';

$id_dokter = $_SESSION['dokter']['id'];

$error = '';
$success = '';

$sql = "SELECT nama, alamat, no_hp FROM dokter WHERE id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $id_dokter);
    $stmt->execute();
    $stmt->bind_result($nama, $alamat, $no_hp);
    $stmt->fetch();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST["nama"];
    $alamat = $_POST["alamat"];
    $no_hp = $_POST["no_hp"];

    if (empty($nama) || empty($alamat) || empty($no_hp)) {
        $error = "Semua kolom harus diisi!";
    } else {
        $sql = "UPDATE dokter SET nama = ?, alamat = ?, no_hp = ? WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssi", $nama, $alamat, $no_hp, $id_dokter);
            if ($stmt->execute()) {
                $success = "Profil berhasil diperbarui!";
            } else {
                $error = "Terjadi kesalahan saat memperbarui profil.";
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
    <title>Profil Dokter</title>
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

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            color: white;
            background-color: #1a3e6d;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #2a5d99;
        }

        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }

        .alert-success {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }

        .alert-danger {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <img src="../assets/img/hospital.svg" alt="Hospital Logo" width="50px">
        <h2>Panel Dokter</h2>
        <a href="dashboard_dokter.php">Jadwal Periksa</a>
        <a href="daftar_periksa_pasien.php">Memeriksa Pasien</a>
        <a href="riwayat_pasien.php">Riwayat Pasien</a>
        <a href="profil_dokter.php" class="active">Profil</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <h1>Profil Dokter</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="profil_dokter.php">
            <div class="form-group">
                <label for="nama">Nama Dokter</label>
                <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($nama); ?>" required>
            </div>
            <div class="form-group">
                <label for="alamat">Alamat Dokter</label>
                <input type="text" id="alamat" name="alamat" value="<?= htmlspecialchars($alamat); ?>" required>
            </div>
            <div class="form-group">
                <label for="no_hp">No Telepon Dokter</label>
                <input type="text" id="no_hp" name="no_hp" value="<?= htmlspecialchars($no_hp); ?>" required>
            </div>
            <button type="submit" class="btn">Simpan</button>
        </form>
    </div>
</body>

</html>