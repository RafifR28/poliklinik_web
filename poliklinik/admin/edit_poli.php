<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login_admin.php");
    exit();
}

require_once 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $nama_poli = $_POST["nama_poli"];
    $keterangan = $_POST["keterangan"];

    $sql = "UPDATE poli SET nama_poli = ?, keterangan = ? WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssi", $nama_poli, $keterangan, $id);
        if ($stmt->execute()) {
            header("Location: kelola_poli.php");
            exit();
        } else {
            echo "Ada yang salah. Silakan coba lagi nanti.";
        }
        $stmt->close();
    }
    $conn->close();
}
?>