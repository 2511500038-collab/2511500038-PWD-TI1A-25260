 <?php
session_start();
require 'koneksi.php';
require 'fungsi.php'; // jika ada fungsi helper

/*
    FORM EDIT BIODATA DOSEN
    Sesuai dengan nomor 4 dan 6 dari soal:
    - Saat link Edit diklik, data yang dipilih ditampilkan pada form
    - Input NIDN hanya readonly (tidak bisa diubah)
    - Tombol Kirim dan Batal mengikuti contoh pada section#contact
*/

// Validasi ID dari GET parameter
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1]
]);

// Jika ID tidak valid, redirect dengan error
if (!$id) {
    $_SESSION['status_crud'] = 'error';
    $_SESSION['message_crud'] = 'ID dosen tidak valid!';
    header("Location: read.php");
    exit();
}

// Ambil data dosen dari database berdasarkan ID
$stmt = mysqli_prepare($conn, 
    "SELECT id, nidn, nama_dosen, jenis_kelamin, tanggal_lahir, 
            email, no_telepon, alamat, jabatan, pendidikan_terakhir 
     FROM biodata_dosen 
     WHERE id = ? LIMIT 1"
);

if (!$stmt) {
    $_SESSION['status_crud'] = 'error';
    $_SESSION['message_crud'] = 'Query tidak valid!';
    header("Location: read.php");
    exit();
}

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$dosen = mysqli_fetch_assoc($stmt);
mysqli_stmt_close($stmt);

// Jika data tidak ditemukan
if (!$dosen) {
    $_SESSION['status_crud'] = 'error';
    $_SESSION['message_crud'] = 'Data dosen tidak ditemukan!';
    header("Location: read.php");
    exit();
}

// Ambil pesan error dan old input dari session jika ada (untuk validasi form)
$status = $_SESSION['status_crud'] ?? '';
$message = $_SESSION['message_crud'] ?? '';
$old_input = $_SESSION['old_input'] ?? [];

// Hapus session setelah diambil
unset($_SESSION['status_crud'], $_SESSION['message_crud'], $_SESSION['old_input']);

// Gunakan old input jika ada, jika tidak gunakan data dari database
$data = !empty($old_input) ? $old_input : $dosen;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Biodata Dosen</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container {
            max-width: 800px;
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
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="tel"],
        .form-group input[type="date"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .form-group input[type="text"]:read-only {
            background-color: #f5f5f5;
            cursor: not-allowed;
        }
        
        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 5px;
        }
        
        .radio-group label {
            display: flex;
            align-items: center;
            font-weight: normal;
        }
        
        .radio-group input[type="radio"] {
            margin-right: 8px;
        }
        
        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .btn-secondary {
            background-color: #95a5a6;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #7f8c8d;
        }
        
        h1 {
            color: #2c3e50;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3498db;
        }
        
        .form-note {
            background-color: #e7f3fe;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #2196F3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Biodata Dosen</h1>
        
        <?php if ($status && $message): ?>
            <div class="status-message <?php echo $status; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="form-note">
            <strong>Catatan:</strong> Field NIDN tidak dapat diubah karena merupakan identitas unik dosen.
        </div>
        
        <form action="proses_update.php" method="POST">
            <!-- Hidden input untuk ID -->
            <input type="hidden" name="id" value="<?php echo (int)$id; ?>">
            
            <div class="form-group">
                <label for="txtNidn">NIDN:</label>
                <input type="text" id="txtNidn" name="txtNidn" 
                       value="<?php echo htmlspecialchars($data['nidn']); ?>" 
                       readonly class="readonly-input">
                <small style="color: #666;">NIDN tidak dapat diubah</small>
            </div>
            
            <div class="form-group">
                <label for="txtNamaDosen">Nama Dosen:</label>
                <input type="text" id="txtNamaDosen" name="txtNamaDosen" 
                       placeholder="Masukkan Nama Lengkap"
                       value="<?php echo htmlspecialchars($data['nama_dosen']); ?>"
                       required>
            </div>
            
            <div class="form-group">
                <label>Jenis Kelamin:</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="txtJenisKelamin" value="L" 
                               <?php echo ($data['jenis_kelamin'] == 'L') ? 'checked' : ''; ?>
                               required> Laki-laki
                    </label>
                    <label>
                        <input type="radio" name="txtJenisKelamin" value="P"
                               <?php echo ($data['jenis_kelamin'] == 'P') ? 'checked' : ''; ?>> Perempuan
                    </label>
                </div>
            </div>
            
            <div class="form-group">
                <label for="txtTanggalLahir">Tanggal Lahir:</label>
                <input type="date" id="txtTanggalLahir" name="txtTanggalLahir"
                       value="<?php echo htmlspecialchars($data['tanggal_lahir']); ?>"
                       required>
            </div>
            
            <div class="form-group">
                <label for="txtEmail">Email:</label>
                <input type="email" id="txtEmail" name="txtEmail" 
                       placeholder="Masukkan Email"
                       value="<?php echo htmlspecialchars($data['email']); ?>"
                       required>
            </div>
            
            <div class="form-group">
                <label for="txtNoTelepon">No Telepon:</label>
                <input type="tel" id="txtNoTelepon" name="txtNoTelepon" 
                       placeholder="Masukkan No Telepon"
                       pattern="[0-9]{10,15}"
                       title="No telepon 10-15 digit angka"
                       value="<?php echo htmlspecialchars($data['no_telepon']); ?>"
                       required>
            </div>
            
            <div class="form-group">
                <label for="txtAlamat">Alamat:</label>
                <textarea id="txtAlamat" name="txtAlamat" rows="3" 
                          placeholder="Masukkan Alamat"
                          required><?php echo htmlspecialchars($data['alamat']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="selJabatan">Jabatan:</label>
                <select id="selJabatan" name="selJabatan" required>
                    <option value="">Pilih Jabatan</option>
                    <option value="Asisten Ahli" <?php echo ($data['jabatan'] == 'Asisten Ahli') ? 'selected' : ''; ?>>Asisten Ahli</option>
                    <option value="Lektor" <?php echo ($data['jabatan'] == 'Lektor') ? 'selected' : ''; ?>>Lektor</option>
                    <option value="Lektor Kepala" <?php echo ($data['jabatan'] == 'Lektor Kepala') ? 'selected' : ''; ?>>Lektor Kepala</option>
                    <option value="Guru Besar" <?php echo ($data['jabatan'] == 'Guru Besar') ? 'selected' : ''; ?>>Guru Besar</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="selPendidikan">Pendidikan Terakhir:</label>
                <select id="selPendidikan" name="selPendidikan" required>
                    <option value="">Pilih Pendidikan</option>
                    <option value="S1" <?php echo ($data['pendidikan_terakhir'] == 'S1') ? 'selected' : ''; ?>>S1</option>
                    <option value="S2" <?php echo ($data['pendidikan_terakhir'] == 'S2') ? 'selected' : ''; ?>>S2</option>
                    <option value="S3" <?php echo ($data['pendidikan_terakhir'] == 'S3') ? 'selected' : ''; ?>>S3</option>
                </select>
            </div>
            
            <div class="button-group">
                <!-- Tombol Kirim (mengikuti contoh section#contact) -->
                <button type="submit" class="btn btn-primary">Kirim</button>
                
                <!-- Tombol Batal (mengikuti contoh section#contact) -->
                <button type="reset" class="btn btn-secondary">Batal</button>
                
                <!-- Link Kembali ke halaman data dosen -->
                <a href="read.php" class="btn btn-secondary">Kembali ke Data Dosen</a>
            </div>
        </form>
    </div>
    
    <script src="script.js"></script>
    <script>
        // Validasi form sebelum submit
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[action="proses_update.php"]');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const telepon = document.getElementById('txtNoTelepon').value;
                    
                    // Validasi No Telepon
                    if (!/^[0-9]{10,15}$/.test(telepon)) {
                        alert('No telepon harus 10-15 digit angka');
                        e.preventDefault();
                        return false;
                    }
                    
                    // Validasi Tanggal Lahir
                    const tanggalLahir = document.getElementById('txtTanggalLahir').value;
                    const today = new Date();
                    const inputDate = new Date(tanggalLahir);
                    const usia = today.getFullYear() - inputDate.getFullYear();
                    
                    if (usia < 25) {
                        alert('Usia dosen minimal 25 tahun');
                        e.preventDefault();
                        return false;
                    } else if (usia > 70) {
                        alert('Usia dosen maksimal 70 tahun');
                        e.preventDefault();
                        return false;
                    }
                    
                    return true;
                });
            }
            
            // Auto-focus ke field pertama yang dapat diisi
            const firstInput = document.getElementById('txtNamaDosen');
            if (firstInput) {
                firstInput.focus();
            }
        });
    </script>
</body>
</html>

<?php
// Tutup koneksi database
mysqli_close($conn);
?>