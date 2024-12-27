<?php
session_start();
require_once '../admin/koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = $_POST['username'];
    $alamat = $_POST['password'];

    $query = $conn->prepare("SELECT * FROM pasien WHERE nama = ?");
    $query->bind_param('s', $nama_lengkap);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $pasien = $result->fetch_assoc();

        if ($alamat === $pasien['alamat']) {
            $_SESSION['pasien'] = $pasien['no_ktp'];
            $_SESSION['id_pasien'] = $pasien['id'];
            $_SESSION['no_rm'] = $pasien['no_rm'];
            header("Location: dashboard_pasien.php");
            exit();
        } else {
            $error = "Alamat salah.";
        }
    } else {
        $error = "Nama tidak ditemukan.";
    }

    $query->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pasien</title>
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
        .login-box input[type="password"] {
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

        .error {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <main>
        <div class="login-box">
            <h2>Login Pasien</h2>
            <form action="" method="post">
                <label for="username">Nama Lengkap:</label>
                <input type="text" id="username" name="username" required>
                <label for="password">Alamat:</label>
                <input type="password" id="password" name="password" required>
                <input type="submit" value="Login">
            </form>
            <?php if (isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>