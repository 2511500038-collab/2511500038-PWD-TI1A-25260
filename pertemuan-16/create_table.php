<?php
// create_table.php
require_once 'koneksi.php';

$sql = "CREATE TABLE IF NOT EXISTS biodata_dosen (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nidn VARCHAR(20) UNIQUE NOT NULL,
    nama_dosen VARCHAR(100) NOT NULL,
    jenis_kelamin ENUM('L', 'P') NOT NULL,
    tanggal_lahir DATE NOT NULL,
    email VARCHAR(100) NOT NULL,
    no_telepon VARCHAR(15) NOT NULL,
    alamat TEXT NOT NULL,
    jabatan VARCHAR(50) NOT NULL,
    pendidikan_terakhir VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $sql)) {
    echo "Tabel biodata_dosen berhasil dibuat!<br>";
    echo '<a href="index.php">Kembali ke Form</a>';
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

mysqli_close($conn);
?>