<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login_admin.php");
    exit();
}

require_once 'koneksi.php';

$nama_poli = $keterangan = "";
$nama_poli_err = $keterangan_err = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (empty(trim($_POST["nama_poli"]))) {
        $nama_poli_err = "Silakan masukkan nama poli.";
    } else {
        $nama_poli = trim($_POST["nama_poli"]);
    }

    
    if (empty(trim($_POST["keterangan"]))) {
        $keterangan_err = "Silakan masukkan keterangan.";
    } else {
        $keterangan = trim($_POST["keterangan"]);
    }

    
    if (empty($nama_poli_err) && empty($keterangan_err)) {
        
        $sql = "INSERT INTO poli (nama_poli, keterangan) VALUES (?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            
            $stmt->bind_param("ss", $param_nama_poli, $param_keterangan);

            
            $param_nama_poli = $nama_poli;
            $param_keterangan = $keterangan;

            if ($stmt->execute()) {
                header("Location: kelola_poli.php");
                exit();
            } else {
                echo "Ada yang salah. Silakan coba lagi nanti.";
            }

            $stmt->close();
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Poli</title>
</head>
<body>
    <h2>Tambah Poli</h2>
    <p>Silakan isi form di bawah ini untuk menambahkan poli baru.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div>
            <label>Nama Poli</label>
            <input type="text" name="nama_poli" value="<?php echo $nama_poli; ?>">
            <span><?php echo $nama_poli_err; ?></span>
        </div>
        <div>
            <label>Keterangan</label>
            <textarea name="keterangan"><?php echo $keterangan; ?></textarea>
            <span><?php echo $keterangan_err; ?></span>
        </div>
        <div>
            <input type="submit" value="Tambah">
        </div>
    </form>
</body>
</html>