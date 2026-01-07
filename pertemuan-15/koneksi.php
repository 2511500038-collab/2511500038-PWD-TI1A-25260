<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_pwd2025";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
  die("Koneksi gagal: " . mysqli_connect_error());
}

function buatTabelMahasiswa($conn) {
    $checkTable = "SHOW TABLES LIKE 'mahasiswa'";
    $result = $conn->query($checkTable);
    
    if ($result->num_rows == 0) {
        $createTable = "CREATE TABLE IF NOT EXISTS mahasiswa (
            id INT PRIMARY KEY AUTO_INCREMENT,
            nim VARCHAR(20) UNIQUE NOT NULL,
            nama VARCHAR(100) NOT NULL,
            tempat_lahir VARCHAR(50),
            tanggal_lahir DATE,
            alamat TEXT,
            email VARCHAR(100),
            telepon VARCHAR(15),
            jenis_kelamin ENUM('L', 'P'),
            program_studi VARCHAR(50),
            hobi TEXT,
            pasangan VARCHAR(100),
            pekerjaan VARCHAR(100),
            nama_ortu VARCHAR(100),
            nama_kakak VARCHAR(100),
            nama_adik VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        if ($conn->query($createTable)) {
            error_log("Tabel mahasiswa berhasil dibuat");
            return true;
        } else {
            error_log("Gagal membuat tabel mahasiswa: " . $conn->error);
            return false;
        }
    }
    return true;
}

// Panggil fungsi untuk membuat tabel
buatTabelMahasiswa($conn);