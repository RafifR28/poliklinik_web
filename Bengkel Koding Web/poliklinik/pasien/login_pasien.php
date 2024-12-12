<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'poliklinik_db');

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $no_ktp = $_POST['username'];
    $no_rm = $_POST['password'];

    $query = $conn->prepare("SELECT * FROM pasien WHERE no_ktp = ?");
    $query->bind_param('s', $no_ktp);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $pasien = $result->fetch_assoc();

        if ($no_rm === $pasien['no_rm']) {
            $_SESSION['pasien'] = $pasien['no_ktp']; 
            header("Location: dashboard_pasien.php");
            exit();
        } else {
            $error = "Nomor Rekam Medis salah.";
        }
    } else {
        $error = "No KTP tidak ditemukan.";
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
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-box {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            box-sizing: border-box;
        }
        .login-box h2 {
            text-align: center;
            color: #007BFF;
            margin-bottom: 20px;
        }
        .login-box label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .login-box input[type="text"],
        .login-box input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }
        .login-box input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
        }
        .login-box input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            text-align: center;
            margin-top: 10px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Login Pasien</h2>
        <form action="" method="post">
            <label for="username">No KTP:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Nomor Rekam Medis:</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Login">
        </form>

        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
    </div>
</body>
</html>