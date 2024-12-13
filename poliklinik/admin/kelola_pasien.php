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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $nama = $_POST["nama"];
    $alamat = $_POST["alamat"];
    $no_ktp = $_POST["no_ktp"];
    $no_hp = $_POST["no_hp"];
    $no_rm = $_POST["no_rm"];

    if ($id) {
        $sql = "UPDATE pasien SET nama = ?, alamat = ?, no_ktp = ?, no_hp = ?, no_rm = ? WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssssi", $nama, $alamat, $no_ktp, $no_hp, $no_rm, $id);
            if ($stmt->execute()) {
                header("Location: kelola_pasien.php");
                exit();
            } else {
                echo "Ada yang salah. Silakan coba lagi nanti.";
            }
            $stmt->close();
        }
    } else {
        $currentYearMonth = date('Ym');
        $sql = "SELECT COUNT(*) as count FROM pasien WHERE no_rm LIKE '$currentYearMonth%'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $count = $row['count'] + 1;
        $no_rm = $currentYearMonth . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        $sql = "INSERT INTO pasien (nama, alamat, no_ktp, no_hp, no_rm) VALUES (?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssss", $nama, $alamat, $no_ktp, $no_hp, $no_rm);
            if ($stmt->execute()) {
                header("Location: kelola_pasien.php");
                exit();
            } else {
                echo "Ada yang salah. Silakan coba lagi nanti.";
            }
            $stmt->close();
        }
    }
    $conn->close();
}

$sql = "SELECT * FROM pasien";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pasien</title>
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

        .form-container {
            background-color: white;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .form-container h3 {
            margin-top: 0;
        }

        .form-container label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-container input[type="text"],
        .form-container textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }

        .form-container input[type="text"]:focus,
        .form-container textarea:focus {
            border-color: #4CAF50;
            outline: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border: 1px solid #ddd;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        .btn {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .btn:hover {
            background-color: #45a049;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            border-radius: 5px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
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
        <div class="form-container">
            <h3>Tambah / Edit Pasien</h3>
            <form id="tambahForm" action="kelola_pasien.php" method="post">
                <input type="hidden" id="tambah_id" name="id">
                <label for="tambah_nama">Nama Pasien:</label>
                <input type="text" id="tambah_nama" name="nama" required>
                <label for="tambah_alamat">Alamat:</label>
                <input type="text" id="tambah_alamat" name="alamat" required>
                <label for="tambah_no_ktp">Nomor KTP:</label>
                <input type="text" id="tambah_no_ktp" name="no_ktp" required>
                <label for="tambah_no_hp">Nomor HP:</label>
                <input type="text" id="tambah_no_hp" name="no_hp" required>
                <label for="tambah_no_rm">Nomor RM:</label>
                <input type="text" id="tambah_no_rm" name="no_rm" readonly>
                <button type="submit" class="btn">Simpan</button>
            </form>
        </div>

        <h3>Daftar Pasien</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pasien</th>
                    <th>Alamat</th>
                    <th>Nomor KTP</th>
                    <th>Nomor HP</th>
                    <th>Nomor RM</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["nama"] . "</td>";
                        echo "<td>" . $row["alamat"] . "</td>";
                        echo "<td>" . $row["no_ktp"] . "</td>";
                        echo "<td>" . $row["no_hp"] . "</td>";
                        echo "<td>" . $row["no_rm"] . "</td>";
                        echo "<td>
                                <button class='btn' onclick='openEditModal(" . $row["id"] . ", \"" . $row["nama"] . "\", \"" . $row["alamat"] . "\", \"" . $row["no_ktp"] . "\", \"" . $row["no_hp"] . "\", \"" . $row["no_rm"] . "\")'>Ubah</button>
                                <button class='btn' onclick='openDeleteModal(" . $row["id"] . ")'>Hapus</button>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>Tidak ada data</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeDeleteModal()">&times;</span>
            <h2>Hapus Pasien</h2>
            <p>Apakah Anda yakin ingin menghapus pasien ini?</p>
            <form id="deleteForm" action="hapus_pasien.php" method="post">
                <input type="hidden" id="delete_id" name="id">
                <button type="submit" class="btn">Hapus</button>
                <button type="button" class="btn" onclick="closeDeleteModal()">Jangan</button>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, nama, alamat, no_ktp, no_hp, no_rm) {
            document.getElementById('tambah_id').value = id;
            document.getElementById('tambah_nama').value = nama;
            document.getElementById('tambah_alamat').value = alamat;
            document.getElementById('tambah_no_ktp').value = no_ktp;
            document.getElementById('tambah_no_hp').value = no_hp;
            document.getElementById('tambah_no_rm').value = no_rm;
            document.getElementById('tambahForm').scrollIntoView();
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = "none";
        }

        function openDeleteModal(id) {
            document.getElementById('delete_id').value = id;
            document.getElementById('deleteModal').style.display = "block";
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('deleteModal')) {
                closeDeleteModal();
            }
        }
    </script>
</body>

</html>