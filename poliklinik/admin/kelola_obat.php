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
    $nama_obat = $_POST["nama_obat"];
    $kemasan = $_POST["kemasan"];
    $harga = $_POST["harga"];

    if ($id) {
        $sql = "UPDATE obat SET nama_obat = ?, kemasan = ?, harga = ? WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssii", $nama_obat, $kemasan, $harga, $id);
            if ($stmt->execute()) {
                header("Location: kelola_obat.php");
                exit();
            } else {
                echo "Ada yang salah. Silakan coba lagi nanti.";
            }
            $stmt->close();
        }
    } else {
        $sql = "INSERT INTO obat (nama_obat, kemasan, harga) VALUES (?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssi", $nama_obat, $kemasan, $harga);
            if ($stmt->execute()) {
                header("Location: kelola_obat.php");
                exit();
            } else {
                echo "Ada yang salah. Silakan coba lagi nanti.";
            }
            $stmt->close();
        }
    }
    $conn->close();
}

$sql = "SELECT * FROM obat";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Obat</title>
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
            <h3>Tambah / Edit Obat</h3>
            <form id="tambahForm" action="kelola_obat.php" method="post">
                <input type="hidden" id="tambah_id" name="id">
                <label for="tambah_nama_obat">Nama Obat:</label>
                <input type="text" id="tambah_nama_obat" name="nama_obat" required>
                <label for="tambah_kemasan">Kemasan:</label>
                <input type="text" id="tambah_kemasan" name="kemasan">
                <label for="tambah_harga">Harga:</label>
                <input type="text" id="tambah_harga" name="harga" required>
                <button type="submit" class="btn">Simpan</button>
            </form>
        </div>

        <h3>Daftar Obat</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Obat</th>
                    <th>Kemasan</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["nama_obat"] . "</td>";
                        echo "<td>" . $row["kemasan"] . "</td>";
                        echo "<td>" . $row["harga"] . "</td>";
                        echo "<td>
                                <button class='btn' onclick='openEditModal(" . $row["id"] . ", \"" . $row["nama_obat"] . "\", \"" . $row["kemasan"] . "\", " . $row["harga"] . ")'>Ubah</button>
                                <button class='btn' onclick='openDeleteModal(" . $row["id"] . ")'>Hapus</button>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Tidak ada data</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeDeleteModal()">&times;</span>
            <h2>Hapus Obat</h2>
            <p>Apakah Anda yakin ingin menghapus obat ini?</p>
            <form id="deleteForm" action="hapus_obat.php" method="post">
                <input type="hidden" id="delete_id" name="id">
                <button type="submit" class="btn">Hapus</button>
                <button type="button" class="btn" onclick="closeDeleteModal()">Jangan</button>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, nama_obat, kemasan, harga) {
            document.getElementById('tambah_id').value = id;
            document.getElementById('tambah_nama_obat').value = nama_obat;
            document.getElementById('tambah_kemasan').value = kemasan;
            document.getElementById('tambah_harga').value = harga;
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