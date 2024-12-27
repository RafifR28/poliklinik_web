<?php
session_start();
require_once '../admin/koneksi.php';

if (!isset($_SESSION['dokter']['id'])) {
    header("Location: login_dokter.php");
    exit();
}

$id_daftar_poli = $_GET['id'];

$sql = "SELECT p.nama, dp.keluhan
        FROM daftar_poli dp
        JOIN pasien p ON dp.id_pasien = p.id
        WHERE dp.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_daftar_poli);
$stmt->execute();
$stmt->bind_result($nama_pasien, $keluhan);
$stmt->fetch();
$stmt->close();

$sql = "SELECT pr.id, pr.tgl_periksa, pr.catatan, pr.biaya_periksa, dp.id_obat, o.harga
        FROM periksa pr
        JOIN detail_periksa dp ON pr.id = dp.id_periksa
        JOIN obat o ON dp.id_obat = o.id
        WHERE pr.id_daftar_poli = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_daftar_poli);
$stmt->execute();
$stmt->bind_result($id_periksa, $tgl_periksa, $catatan, $biaya_periksa, $id_obat, $harga_obat);
$periksa_exists = $stmt->fetch();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tgl_periksa = $_POST["tgl_periksa"];
    $catatan = $_POST["catatan"];
    $id_obat = $_POST["id_obat"];
    $harga_obat = $_POST["harga_obat"];
    $biaya_jasa_dokter = 150000;
    $total_harga = $harga_obat + $biaya_jasa_dokter;

    if ($periksa_exists) {
        $sql = "UPDATE periksa SET tgl_periksa = ?, catatan = ?, biaya_periksa = ? WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssii", $tgl_periksa, $catatan, $total_harga, $id_periksa);
            if ($stmt->execute()) {
                $sql_detail = "UPDATE detail_periksa SET id_obat = ? WHERE id_periksa = ?";
                if ($stmt_detail = $conn->prepare($sql_detail)) {
                    $stmt_detail->bind_param("ii", $id_obat, $id_periksa);
                    $stmt_detail->execute();
                    $stmt_detail->close();
                }
                header("Location: daftar_periksa_pasien.php");
                exit();
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
            $stmt->close();
        }
    } else {
        $sql = "INSERT INTO periksa (id_daftar_poli, tgl_periksa, catatan, biaya_periksa) VALUES (?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("issi", $id_daftar_poli, $tgl_periksa, $catatan, $total_harga);
            if ($stmt->execute()) {
                $id_periksa = $stmt->insert_id;
                $sql_detail = "INSERT INTO detail_periksa (id_periksa, id_obat) VALUES (?, ?)";
                if ($stmt_detail = $conn->prepare($sql_detail)) {
                    $stmt_detail->bind_param("ii", $id_periksa, $id_obat);
                    $stmt_detail->execute();
                    $stmt_detail->close();
                }
                header("Location: daftar_periksa_pasien.php");
                exit();
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
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
    <title>Periksa Pasien</title>
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

        input, select, textarea {
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Periksa Pasien</h1>
        <form action="periksa_pasien.php?id=<?= $id_daftar_poli; ?>" method="POST">
            <div class="form-group">
                <label for="nama_pasien">Nama Pasien</label>
                <input type="text" id="nama_pasien" name="nama_pasien" value="<?= htmlspecialchars($nama_pasien); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="tgl_periksa">Tanggal Periksa</label>
                <input type="date" id="tgl_periksa" name="tgl_periksa" value="<?= htmlspecialchars($tgl_periksa ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="catatan">Catatan</label>
                <textarea id="catatan" name="catatan" required><?= htmlspecialchars($catatan ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="id_obat">Obat</label>
                <select id="id_obat" name="id_obat" required>
                    <?php
                    $sql_obat = "SELECT id, nama_obat, harga FROM obat";
                    $result_obat = $conn->query($sql_obat);
                    while ($row_obat = $result_obat->fetch_assoc()) {
                        $selected = ($row_obat['id'] == $id_obat) ? 'selected' : '';
                        echo "<option value='" . $row_obat['id'] . "' data-harga='" . $row_obat['harga'] . "' $selected>" . $row_obat['nama_obat'] . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="harga_obat">Harga Obat</label>
                <input type="text" id="harga_obat" name="harga_obat" value="<?= htmlspecialchars($harga_obat ?? ''); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="total_harga">Total Harga</label>
                <input type="text" id="total_harga" name="total_harga" value="<?= htmlspecialchars($biaya_periksa ?? ''); ?>" readonly>
            </div>
            <button type="submit" class="btn">Simpan</button>
        </form>
    </div>

    <script>
        document.getElementById('id_obat').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var harga = selectedOption.getAttribute('data-harga');
            document.getElementById('harga_obat').value = harga;
            var totalHarga = parseInt(harga) + 150000;
            document.getElementById('total_harga').value = totalHarga;
        });
    </script>
</body>
</html>