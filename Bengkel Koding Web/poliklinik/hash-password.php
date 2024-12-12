<?php
$conn = new mysqli('localhost', 'root', '', 'poliklinik_db');

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$username = 'drhendri';
$new_password = password_hash('drhendri', PASSWORD_DEFAULT);

$query = $conn->prepare("UPDATE login_dokter SET password = ? WHERE username = ?");
$query->bind_param('ss', $new_password, $username);

if ($query->execute()) {
    echo "Password berhasil di-hash dan diperbarui untuk username: $username";
} else {
    echo "Gagal memperbarui password: " . $query->error;
}

$query->close();
$conn->close();
?>