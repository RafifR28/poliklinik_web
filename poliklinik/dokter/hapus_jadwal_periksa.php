<?php
session_start();
require_once '../admin/koneksi.php';

if (!isset($_SESSION['dokter']['id'])) {
    header("Location: login_dokter.php");
    exit();
}

$id_dokter = $_SESSION['dokter']['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_jadwal = $_POST['id'];

    $sql = "DELETE FROM jadwal_periksa WHERE id = ? AND id_dokter = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $id_jadwal, $id_dokter);
        if ($stmt->execute()) {
            header("Location: dashboard_dokter.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        $stmt->close();
    }
    $conn->close();
}
?>