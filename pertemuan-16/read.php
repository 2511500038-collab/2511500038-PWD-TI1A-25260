 <?php
session_start();
require_once 'koneksi.php';
require_once 'fungsi.php'; // jika ada fungsi bantu seperti formatTanggal

/*
    FILE PEMBACA DATA DOSEN
    Sesuai dengan nomor 3: Menampilkan data dengan link Edit & Delete
    Sesuai dengan nomor 5: Menampilkan status sukses/gagal dari UPDATE
    Sesuai dengan nomor 6: Menampilkan status sukses/gagal dari UPDATE
    Sesuai dengan nomor 8: Menampilkan status sukses/gagal dari DELETE
*/

// Ambil pesan status dari session untuk operasi CRUD
$status_crud = $_SESSION['status_crud'] ?? '';
$message_crud = $_SESSION['message_crud'] ?? '';

// Hapus session setelah ditampilkan
unset($_SESSION['status_crud'], $_SESSION['message_crud']);

// Query untuk mengambil data dosen
$sql = "SELECT * FROM biodata_dosen ORDER BY created_at DESC";
$q = mysqli_query($conn, $sql);

// Cek jika query error
if (!$q) {
    die("Query error: " . mysqli_error($conn));
}

// Hitung total data
$total_data = mysqli_num_rows($q);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Dosen - File Pembaca</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .status-message {
            padding: 12px 20px;
            margin: 15px 0;
            border-radius: 5px;
            font-weight: bold;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #2196F3;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        
        .btn-primary {
            background-color: #4CAF50;
        }
        
        .btn-danger {
            background-color: #f44336;
        }
        
        .btn:hover {
            opacity: 0.9;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #333;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        .action-links a {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            text-decoration: none;
            margin-right: 5px;
            font-size: 14px;
        }
        
        .edit-link {
            background-color: #4CAF50;
            color: white;
        }
        
        .delete-link {
            background-color: #f44336;
            color: white;
        }
        
        .info-box {
            background-color: #e7f3fe;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 18px;
        }
        
        .summary {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Data Dosen</h1>
            <a href="index.php" class="btn btn-primary">Tambah Data Baru</a>
        </div>
        
        <?php if ($status_crud && $message_crud): ?>
            <div class="status-message <?php echo $status_crud; ?>">
                <?php echo htmlspecialchars($message_crud); ?>
            </div>
        <?php endif; ?>
        
        <div class="info-box">
            <strong>Informasi:</strong> Halaman ini menampilkan data dosen yang tersimpan dalam database. 
            Anda dapat mengedit atau menghapus data dengan mengklik tombol aksi yang tersedia.
        </div>
        
        <div class="summary">
            <div>
                <strong>Total Data:</strong> <?php echo $total_data; ?> dosen
            </div>
            <div>
                <strong>Terakhir diupdate:</strong> <?php echo date('d/m/Y H:i:s'); ?>
            </div>
        </div>
        
        <?php if ($total_data > 0): ?>
            <table border="1" cellpadding="8" cellspacing="0">
                <tr>
                    <th>No</th>
                    <th>Aksi</th>
                    <th>NIDN</th>
                    <th>Nama Dosen</th>
                    <th>Jenis Kelamin</th>
                    <th>Tanggal Lahir</th>
                    <th>Email</th>
                    <th>No Telepon</th>
                    <th>Jabatan</th>
                    <th>Pendidikan</th>
                    <th>Created At</th>
                </tr>
                
                <?php $i = 1; ?>
                <?php while ($row = mysqli_fetch_assoc($q)): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td class="action-links">
                            <!-- LINK EDIT - Sesuai nomor 4: Saat link Edit diklik -->
                            <a href="edit_dosen.php?id=<?php echo (int)$row['id']; ?>" 
                               class="edit-link" title="Edit data">
                                Edit
                            </a>
                            
                            <!-- LINK DELETE - Sesuai nomor 7: Konfirmasi penghapusan -->
                            <a onclick="return confirm('Yakin menghapus data <?php echo htmlspecialchars($row['nama_dosen']); ?>?')" 
                               href="proses_delete.php?id=<?php echo (int)$row['id']; ?>" 
                               class="delete-link" title="Hapus data">
                                Delete
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($row['nidn']); ?></td>
                        <td><?php echo htmlspecialchars($row['nama_dosen']); ?></td>
                        <td>
                            <?php 
                            if ($row['jenis_kelamin'] == 'L') {
                                echo 'Laki-laki';
                            } else {
                                echo 'Perempuan';
                            }
                            ?>
                        </td>
                        <td>
                            <?php 
                            // Format tanggal Indonesia
                            $tanggal_lahir = date_create($row['tanggal_lahir']);
                            echo date_format($tanggal_lahir, 'd/m/Y');
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['no_telepon']); ?></td>
                        <td><?php echo htmlspecialchars($row['jabatan']); ?></td>
                        <td><?php echo htmlspecialchars($row['pendidikan_terakhir']); ?></td>
                        <td>
                            <?php 
                            // Format tanggal created_at
                            if (isset($row['created_at']) && !empty($row['created_at'])) {
                                $created_at = date_create($row['created_at']);
                                echo date_format($created_at, 'd/m/Y H:i');
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
            
            <div style="margin-top: 20px; text-align: center;">
                <p>Menampilkan <?php echo $total_data; ?> data dosen</p>
            </div>
            
        <?php else: ?>
            <div class="no-data">
                <p>Belum ada data dosen.</p>
                <p>Silakan tambah data baru melalui <a href="index.php">Form Biodata Dosen</a></p>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 30px; text-align: center;">
            <a href="index.php" class="btn">Kembali ke Form</a>
            <button onclick="window.print()" class="btn">Cetak Data</button>
        </div>
    </div>
    
    <script>
        // Konfirmasi sebelum menghapus (sesuai nomor 7)
        document.addEventListener('DOMContentLoaded', function() {
            const deleteLinks = document.querySelectorAll('.delete-link');
            deleteLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                        e.preventDefault();
                    }
                });
            });
            
            // Auto-hide pesan status setelah 5 detik
            const statusMessage = document.querySelector('.status-message');
            if (statusMessage) {
                setTimeout(() => {
                    statusMessage.style.transition = 'opacity 0.5s';
                    statusMessage.style.opacity = '0';
                    setTimeout(() => {
                        statusMessage.style.display = 'none';
                    }, 500);
                }, 5000);
            }
        });
    </script>
</body>
</html>

<?php
// Tutup koneksi database
mysqli_close($conn);
?>