 <?php
session_start();
require_once __DIR__ . '/koneksi.php';

/*
    PROSES INSERT DATA DOSEN
    Sesuai dengan nomor 2 soal: Validasi, sanitasi, PRG, dan insert ke tabel
*/

// Inisialisasi variabel
$errors = [];
$input = [];
$success = false;

// Fungsi sanitasi
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Cek jika form dikirim dengan method POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Sanitasi semua input
    $input['nidn'] = sanitize($_POST['txtNidn'] ?? '');
    $input['nama_dosen'] = sanitize($_POST['txtNamaDosen'] ?? '');
    $input['jenis_kelamin'] = sanitize($_POST['txtJenisKelamin'] ?? '');
    $input['tanggal_lahir'] = sanitize($_POST['txtTanggalLahir'] ?? '');
    $input['email'] = sanitize($_POST['txtEmail'] ?? '');
    $input['no_telepon'] = sanitize($_POST['txtNoTelepon'] ?? '');
    $input['alamat'] = sanitize($_POST['txtAlamat'] ?? '');
    $input['jabatan'] = sanitize($_POST['selJabatan'] ?? '');
    $input['pendidikan_terakhir'] = sanitize($_POST['selPendidikan'] ?? '');
    
    // VALIDASI DATA (sesuai ketentuan)
    
    // 1. Validasi NIDN (10-20 angka, harus unik)
    if (empty($input['nidn'])) {
        $errors[] = "NIDN harus diisi";
    } elseif (!preg_match('/^[0-9]{10,20}$/', $input['nidn'])) {
        $errors[] = "NIDN harus terdiri dari 10-20 angka";
    } else {
        // Cek ke database apakah NIDN sudah ada
        $check_sql = "SELECT id FROM biodata_dosen WHERE nidn = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "s", $input['nidn']);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);
        
        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $errors[] = "NIDN sudah terdaftar";
        }
        mysqli_stmt_close($check_stmt);
    }
    
    // 2. Validasi Nama
    if (empty($input['nama_dosen'])) {
        $errors[] = "Nama dosen harus diisi";
    } elseif (strlen($input['nama_dosen']) < 3) {
        $errors[] = "Nama dosen minimal 3 karakter";
    }
    
    // 3. Validasi Jenis Kelamin
    if (empty($input['jenis_kelamin']) || !in_array($input['jenis_kelamin'], ['L', 'P'])) {
        $errors[] = "Jenis kelamin harus dipilih";
    }
    
    // 4. Validasi Tanggal Lahir
    if (empty($input['tanggal_lahir'])) {
        $errors[] = "Tanggal lahir harus diisi";
    } else {
        // Validasi usia (min 25 tahun, max 70 tahun)
        $tanggal_lahir = new DateTime($input['tanggal_lahir']);
        $today = new DateTime();
        $usia = $today->diff($tanggal_lahir)->y;
        
        if ($usia < 25) {
            $errors[] = "Usia dosen minimal 25 tahun";
        } elseif ($usia > 70) {
            $errors[] = "Usia dosen maksimal 70 tahun";
        }
    }
    
    // 5. Validasi Email
    if (empty($input['email'])) {
        $errors[] = "Email harus diisi";
    } elseif (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    }
    
    // 6. Validasi No Telepon
    if (empty($input['no_telepon'])) {
        $errors[] = "No telepon harus diisi";
    } elseif (!preg_match('/^[0-9]{10,15}$/', $input['no_telepon'])) {
        $errors[] = "No telepon harus 10-15 digit angka";
    }
    
    // 7. Validasi Alamat
    if (empty($input['alamat'])) {
        $errors[] = "Alamat harus diisi";
    } elseif (strlen($input['alamat']) < 10) {
        $errors[] = "Alamat terlalu singkat";
    }
    
    // 8. Validasi Jabatan
    if (empty($input['jabatan']) || !in_array($input['jabatan'], ['Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Guru Besar'])) {
        $errors[] = "Jabatan harus dipilih";
    }
    
    // 9. Validasi Pendidikan Terakhir
    if (empty($input['pendidikan_terakhir']) || !in_array($input['pendidikan_terakhir'], ['S1', 'S2', 'S3'])) {
        $errors[] = "Pendidikan terakhir harus dipilih";
    }
    
    // JIKA TIDAK ADA ERROR, LAKUKAN INSERT KE DATABASE
    if (empty($errors)) {
        // Persiapan query INSERT
        $sql = "INSERT INTO biodata_dosen 
                (nidn, nama_dosen, jenis_kelamin, tanggal_lahir, email, 
                 no_telepon, alamat, jabatan, pendidikan_terakhir) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            // Bind parameter
            mysqli_stmt_bind_param($stmt, "sssssssss",
                $input['nidn'],
                $input['nama_dosen'],
                $input['jenis_kelamin'],
                $input['tanggal_lahir'],
                $input['email'],
                $input['no_telepon'],
                $input['alamat'],
                $input['jabatan'],
                $input['pendidikan_terakhir']
            );
            
            // Eksekusi query
            if (mysqli_stmt_execute($stmt)) {
                $success = true;
                $last_id = mysqli_insert_id($conn);
                
                // Set session untuk flash message sukses
                $_SESSION['status_crud'] = 'success';
                $_SESSION['message_crud'] = 'Data dosen berhasil disimpan!';
                
                // Hapus old input dari session
                unset($_SESSION['old_input']);
                
                // Tutup statement
                mysqli_stmt_close($stmt);
                
                // KONSEP PRG (Post-Redirect-Get)
                header("Location: index.php");
                exit();
                
            } else {
                $errors[] = "Gagal menyimpan data: " . mysqli_error($conn);
            }
        } else {
            $errors[] = "Gagal menyiapkan query: " . mysqli_error($conn);
        }
    }
    
    // JIKA ADA ERROR, SIMPAN KE SESSION UNTUK DITAMPILKAN KEMBALI
    if (!empty($errors)) {
        // Set session untuk flash message error
        $_SESSION['status_crud'] = 'error';
        $_SESSION['message_crud'] = implode("<br>", $errors);
        
        // Simpan input lama ke session untuk ditampilkan kembali di form
        $_SESSION['old_input'] = $input;
        
        // KONSEP PRG (Post-Redirect-Get) - kembali ke form
        header("Location: index.php#biodata");
        exit();
    }
    
} else {
    // Jika bukan POST request, redirect ke halaman utama
    header("Location: index.php");
    exit();
}

// Tutup koneksi database
mysqli_close($conn);
?>