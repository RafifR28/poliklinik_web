<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login_admin.php");
    exit();
}

require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = isset($_POST['nama']) ? trim($_POST['nama']) : null;
    $alamat = isset($_POST['alamat']) ? trim($_POST['alamat']) : null;
    $no_hp = isset($_POST['no_hp']) ? trim($_POST['no_hp']) : null;
    $id_poli = isset($_POST['id_poli']) ? intval($_POST['id_poli']) : null;

    if ($nama && $alamat && $no_hp && $id_poli) {
        $stmt = $conn->prepare("INSERT INTO dokter (nama, alamat, no_hp, id_poli) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $nama, $alamat, $no_hp, $id_poli);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Dokter berhasil ditambahkan.";
        } else {
            $_SESSION['error'] = "Gagal menambahkan dokter. Silakan coba lagi.";
        }

        $stmt->close();
    } else {
        $_SESSION['error'] = "Semua data wajib diisi.";
    }
}

header("Location: kelola_dokter.php");
exit();
?>