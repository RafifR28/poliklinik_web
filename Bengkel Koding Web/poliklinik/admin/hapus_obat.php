<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login_admin.php");
    exit();
}

require_once 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];

    $sql = "DELETE FROM obat WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            header("Location: kelola_obat.php");
            exit();
        } else {
            echo "Ada yang salah. Silakan coba lagi nanti.";
        }
        $stmt->close();
    }
    $conn->close();
}
?>