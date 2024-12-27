<?php
session_start();
require_once '../admin/koneksi.php';

if (!isset($_SESSION['dokter']['id'])) {
    header("Location: login_dokter.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $tgl_periksa = $_POST["tgl_periksa"];
    $catatan = $_POST["catatan"];
    $id_obat = $_POST["id_obat"];
    $harga = $_POST["harga"];

    $sql = "INSERT INTO periksa (id_daftar_poli, tgl_periksa, catatan, biaya_periksa) VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE tgl_periksa = VALUES(tgl_periksa), catatan = VALUES(catatan), biaya_periksa = VALUES(biaya_periksa)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("isss", $id, $tgl_periksa, $catatan, $harga);
        if ($stmt->execute()) {
            $sql_detail = "INSERT INTO detail_periksa (id_periksa, id_obat) VALUES (?, ?)
                           ON DUPLICATE KEY UPDATE id_obat = VALUES(id_obat)";
            if ($stmt_detail = $conn->prepare($sql_detail)) {
                $id_periksa = $stmt->insert_id;
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
    $conn->close();
}
?>