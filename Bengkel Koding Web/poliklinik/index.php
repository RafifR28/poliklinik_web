<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Poliklinik - Sistem Poliklinik Pemeriksaan Kesehatan">
    <link rel="icon" href="/poliklinik/assets/img/hospital.svg" type="image/svg">
    <title>Poliklinik</title>
    <style>
        body {
            font-family: sans-serif;
        }
        div.header {
            width: 100%;
            background-color: rgb(0, 213, 255);
            padding: 20px 0;
            text-align: center;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        div.header h2 {
            color: white;
            margin: 0;
        }

        div.header p {
            color: white;
            margin: 0;
        }

        body {
            margin-top: 80px;
        }

        .content-login {
            margin-top: 250px;
            display: flex;
            justify-content: space-around;
            padding: 0 20px;
            flex-wrap: wrap;
        }

        .content-patient, .content-admin, .content-doctor {
            border: 2px solid #51A2FF;
            padding: 20px;
            width: 45%;
            text-align: center;
            margin-bottom: 20px;
        }

        div.content-patient p a, div.content-admin p a, div.content-doctor p a {
            text-decoration: none;
            border: 2px solid #51A2FF;
            background-color: #51A2FF;
            color: white;
            padding: 10px 20px;
        }

        .link-daftar-pasien {
            margin-top: 30px;
        }

        .content-header {
            text-align: center;
            margin: 120px 0 20px 0;
            padding: 20px;
            margin-top: 100px;
            background-color: #f0f8ff;
            border: 2px solid #51A2FF;
            border-radius: 10px;
        }

        .content-header p {
            font-size: 1.2em;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Poliklinik</h2>
        <p>Sistem Manajemen Poliklinik</p>
    </div>
    <div class="content-header">
        <p>Selamat datang di Sistem Poliklinik. Silakan login atau daftar untuk melanjutkan.</p>
    </div>
    <div class="content-login">
        <div class="content-patient">
            <img src="assets/img/human.svg" alt="Patient Hospital" style="width: 100px; height: 100px;">
            <h2>Registrasi Pasien</h2>
            <p>Pendaftaran Pasien, silakan melakukan login dahulu pada tombol dibawah ini</p>
            <p class="link-pasien"><a href="http://localhost/poliklinik/pasien/login_pasien.php">Login Pasien</a></p>
            <p>Jika belum mendaftar, silakan klik tombol dibawah</p>
            <p class="link-daftar-pasien"><a href="http://localhost/poliklinik/pasien/daftar_pasien.php">Daftar Pasien</a></p>
        </div>
        <div class="content-admin">
            <img src="assets/img/admin-logo.svg" alt="Admin" style="width: 100px; height: 100px;">
            <h2>Admin</h2>
            <p>Area khusus untuk admin sistem</p>
            <p class="link-admin"><a href="http://localhost/poliklinik/admin/login_admin.php">Login Admin</a></p>
        </div>
        <div class="content-doctor">
            <img src="assets/img/doctor-logo.svg" alt="Doctor" style="width: 100px; height: 100px;">
            <h2>Login Dokter</h2>
            <p>Area khusus Login untuk Dokter</p>
            <p class="link-doctor"><a href="http://localhost/poliklinik/dokter/login_dokter.php">Login Dokter</a></p>
        </div>
    </div>
</body>
</html>
