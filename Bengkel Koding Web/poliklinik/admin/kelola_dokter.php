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

$sql_dokter = "SELECT dokter.*, poli.nama_poli FROM dokter 
               INNER JOIN poli ON dokter.id_poli = poli.id
               ORDER BY dokter.id ASC";
$result_dokter = $conn->query($sql_dokter);

$sql_poli = "SELECT * FROM poli";
$result_poli = $conn->query($sql_poli);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Dokter</title>
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
        .form-container textarea,
        .form-container select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .form-container input[type="text"]:focus,
        .form-container select:focus {
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
            background-color: rgba(0,0,0,0.4); 
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 80%; 
            max-width: 600px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            animation: slide-down 0.4s ease-out;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .modal-header h3 {
            margin: 0;
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }

        @keyframes slide-down {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
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
            <h3 id="form-title">Tambah Dokter</h3>
            <form id="dokterForm" action="tambah_dokter.php" method="POST">
                <input type="hidden" id="dokter_id" name="id">
                <label for="nama_dokter">Nama Dokter:</label>
                <input type="text" id="nama_dokter" name="nama" required>
                <label for="alamat">Alamat:</label>
                <input type="text" id="alamat" name="alamat" required>
                <label for="no_hp">No HP:</label>
                <input type="text" id="no_hp" name="no_hp" maxlength="15" required>
                <label for="id_poli">Poli:</label>
                <select id="id_poli" name="id_poli" required>
                    <option value="" disabled selected>Pilih Poli</option>
                    <?php
                    while($row = $result_poli->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . $row['nama_poli'] . "</option>";
                    }
                    ?>
                </select>
                <button type="submit" class="btn" id="submitBtn">Tambah</button>
            </form>
        </div>

        <h3>Daftar Dokter</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Dokter</th>
                    <th>Alamat</th>
                    <th>No HP</th>
                    <th>Poli</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $result_dokter->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td>" . $row["nama"] . "</td>";
                    echo "<td>" . $row["alamat"] . "</td>";
                    echo "<td>" . $row["no_hp"] . "</td>";
                    echo "<td>" . $row["nama_poli"] . "</td>";
                    echo "<td>
                            <button class='btn' onclick=\"editDokter('" . $row['id'] . "', '" . $row['nama'] . "', '" . $row['alamat'] . "', '" . $row['no_hp'] . "', '" . $row['id_poli'] . "')\">Edit</button>
                            <button class='btn' onclick=\"openDeleteModal('" . $row['id'] . "')\">Hapus</button>
                          </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('deleteModal')">&times;</span>
            <h3>Hapus Dokter</h3>
            <p>Apakah Anda yakin ingin menghapus data ini?</p>
            <form action="hapus_dokter.php" method="POST">
                <input type="hidden" id="delete_id" name="id">
                <button type="submit" class="btn">Hapus</button>
                <button type="button" class="btn" onclick="closeModal('deleteModal')">Batal</button>
            </form>
        </div>
    </div>

    <script>
        function editDokter(id, nama, alamat, no_hp, id_poli) {
            document.getElementById('dokter_id').value = id;
            document.getElementById('nama_dokter').value = nama;
            document.getElementById('alamat').value = alamat;
            document.getElementById('no_hp').value = no_hp;
            document.getElementById('id_poli').value = id_poli;
            document.getElementById('form-title').innerText = 'Edit Dokter';
            document.getElementById('submitBtn').innerText = 'Simpan';
            document.getElementById('dokterForm').action = 'edit_dokter.php';
        }

        function openDeleteModal(id) {
            document.getElementById('delete_id').value = id;
            document.getElementById('deleteModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        document.getElementById('no_hp').addEventListener('input', function (e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        window.onclick = function(event) {
            const deleteModal = document.getElementById('deleteModal');
            if (event.target == deleteModal) {
                deleteModal.style.display = 'none';
            }
        }
    </script>
</body>
</html>