<?php
session_start();
require_once '../admin/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_lengkap = $_POST["nama_lengkap"];
    $alamat = $_POST["alamat"];
    $no_ktp = $_POST["no_ktp"];
    $no_hp = $_POST["no_hp"];

    $currentYearMonth = date('Ym');
    $sql = "SELECT COUNT(*) as count FROM pasien WHERE no_rm LIKE '$currentYearMonth%'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $count = $row['count'] + 1;
    $no_rm = $currentYearMonth . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

    $sql = "INSERT INTO pasien (nama, alamat, no_ktp, no_hp, no_rm) VALUES (?, ?, ?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssss", $nama_lengkap, $alamat, $no_ktp, $no_hp, $no_rm);
        if ($stmt->execute()) {
            header("Location: login_pasien.php?success=1");
            exit();
        } else {
            echo "Ada yang salah. Silakan coba lagi nanti.";
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Pasien</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-box {
            width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .login-box h2 {
            margin: 0 0 20px;
            font-weight: 700;
            text-align: center;
        }

        .login-box label {
            display: block;
            margin-bottom: 5px;
            font-weight: 700;
        }

        .login-box input[type="text"],
        .login-box input[type="password"],
        .login-box input[type="email"],
        .login-box input[type="tel"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .login-box input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
        }

        .login-box input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .login-box .login-link {
            text-align: center;
            margin-top: 10px;
        }

        .login-box .login-link a {
            color: #007bff;
            text-decoration: none;
        }

        .login-box .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <main>
        <div class="login-box">
            <h2>Pendaftaran Pasien</h2>
            <form action="" method="post">
                <label for="nama_lengkap">Nama Lengkap</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" required>
                <label for="alamat">Alamat</label>
                <input type="text" id="alamat" name="alamat" required>
                <label for="no_ktp">No KTP</label>
                <input type="text" id="no_ktp" name="no_ktp" required>
                <label for="no_hp">No HP</label>
                <input type="tel" id="no_hp" name="no_hp" required>
                <input type="submit" value="Daftar">
            </form>
            <div class="login-link">
                <p>Sudah pernah mendaftar? <a href="login_pasien.php">Login di sini</a></p>
            </div>
        </div>
    </main>
</body>
</html>