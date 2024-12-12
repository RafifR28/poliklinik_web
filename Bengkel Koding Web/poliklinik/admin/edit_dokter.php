<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login_admin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $conn->real_escape_string($_POST['id']);
    $nama = $conn->real_escape_string($_POST['nama']);
    $alamat = $conn->real_escape_string($_POST['alamat']);
    $no_hp = $conn->real_escape_string($_POST['no_hp']);
    $id_poli = $conn->real_escape_string($_POST['id_poli']);

    $sql = "UPDATE dokter SET nama='$nama', alamat='$alamat', no_hp='$no_hp', id_poli='$id_poli' WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        header("Location: kelola_dokter.php?success=Dokter berhasil diubah");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
$conn->close();
?>
