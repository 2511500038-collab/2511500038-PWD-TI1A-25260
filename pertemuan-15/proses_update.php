 <?php
session_start();
require __DIR__ . '/koneksi.php';
require_once __DIR__ . '/fungsi.php';

#cek method form, hanya izinkan POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash_error'] = 'Akses tidak valid.';
    redirect_ke('read.php');
}

# Tentukan form mana yang sedang diproses
$isBiodataForm = isset($_POST['form_type']) && $_POST['form_type'] === 'biodata_mahasiswa';

if (!$isBiodataForm) {
    // ============================================
    // BAGIAN 1: PROSES UPDATE DATA TAMU (SUDAH ADA)
    // ============================================
    
    #validasi cid wajib angka dan > 0
    $cid = filter_input(INPUT_POST, 'cid', FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1]
    ]);

    if (!$cid) {
        $_SESSION['flash_error'] = 'CID Tidak Valid.';
        redirect_ke('edit.php?cid='. (int)$cid);
    }

    #ambil dan bersihkan (sanitasi) nilai dari form
    $nama  = bersihkan($_POST['txtNamaEd']  ?? '');
    $email = bersihkan($_POST['txtEmailEd'] ?? '');
    $pesan = bersihkan($_POST['txtPesanEd'] ?? '');
    $captcha = bersihkan($_POST['txtCaptcha'] ?? '');

    #Validasi sederhana
    $errors = []; #ini array untuk menampung semua error yang ada

    if ($nama === '') {
        $errors[] = 'Nama wajib diisi.';
    }

    if ($email === '') {
        $errors[] = 'Email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format e-mail tidak valid.';
    }

    if ($pesan === '') {
        $errors[] = 'Pesan wajib diisi.';
    }

    if ($captcha === '') {
        $errors[] = 'Pertanyaan wajib diisi.';
    }

    if (mb_strlen($nama) < 3) {
        $errors[] = 'Nama minimal 3 karakter.';
    }

    if (mb_strlen($pesan) < 10) {
        $errors[] = 'Pesan minimal 10 karakter.';
    }

    if ($captcha!=="6") {
        $errors[] = 'Jawaban '. $captcha.' captcha salah.';
    }

    /*
    kondisi di bawah ini hanya dikerjakan jika ada error, 
    simpan nilai lama dan pesan error, lalu redirect (konsep PRG)
    */
    if (!empty($errors)) {
        $_SESSION['old'] = [
            'nama'  => $nama,
            'email' => $email,
            'pesan' => $pesan
        ];

        $_SESSION['flash_error'] = implode('<br>', $errors);
        redirect_ke('edit.php?cid='. (int)$cid);
    }

    /*
        Prepared statement untuk anti SQL injection.
        menyiapkan query UPDATE dengan prepared statement 
        (WAJIB WHERE cid = ?)
    */
    $stmt = mysqli_prepare($conn, "UPDATE tbl_tamu 
                                    SET cnama = ?, cemail = ?, cpesan = ? 
                                    WHERE cid = ?");
    if (!$stmt) {
        #jika gagal prepare, kirim pesan error (tanpa detail sensitif)
        $_SESSION['flash_error'] = 'Terjadi kesalahan sistem (prepare gagal).';
        redirect_ke('edit.php?cid='. (int)$cid);
    }

    #bind parameter dan eksekusi (s = string, i = integer)
    mysqli_stmt_bind_param($stmt, "sssi", $nama, $email, $pesan, $cid);

    if (mysqli_stmt_execute($stmt)) { #jika berhasil, kosongkan old value
        unset($_SESSION['old']);
        /*
        Redirect balik ke read.php dan tampilkan info sukses.
        */
        $_SESSION['flash_sukses'] = 'Terima kasih, data Anda sudah diperbaharui.';
        redirect_ke('read.php'); #pola PRG: kembali ke data dan exit()
    } else { #jika gagal, simpan kembali old value dan tampilkan error umum
        $_SESSION['old'] = [
            'nama'  => $nama,
            'email' => $email,
            'pesan' => $pesan,
        ];
        $_SESSION['flash_error'] = 'Data gagal diperbaharui. Silakan coba lagi.';
        redirect_ke('edit.php?cid='. (int)$cid);
    }
    #tutup statement
    mysqli_stmt_close($stmt);

} else {
    // ============================================
    // BAGIAN 2: PROSES UPDATE BIODATA MAHASISWA (SOAL NOMOR 5)
    // ============================================
    
    #validasi ID wajib angka dan > 0
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1]
    ]);

    if (!$id) {
        $_SESSION['flash_error'] = 'ID Tidak Valid.';
        redirect_ke('read.php');
    }

    #ambil dan bersihkan nilai dari form edit biodata mahasiswa
    $nim = bersihkan($_POST['nim'] ?? '');
    $nama = bersihkan($_POST['nama'] ?? '');
    $tempat = bersihkan($_POST['tempat'] ?? '');
    $tanggal = bersihkan($_POST['tanggal'] ?? '');
    $alamat = bersihkan($_POST['alamat'] ?? '');
    $email = bersihkan($_POST['email'] ?? '');
    $telepon = bersihkan($_POST['telepon'] ?? '');
    $jenis_kelamin = bersihkan($_POST['jenis_kelamin'] ?? '');
    $program_studi = bersihkan($_POST['program_studi'] ?? '');
    $hobi = bersihkan($_POST['hobi'] ?? '');

    #Validasi data
    $errors = [];

    // Validasi Nama (wajib)
    if ($nama === '') {
        $errors[] = 'Nama lengkap wajib diisi.';
    } elseif (mb_strlen($nama) < 3) {
        $errors[] = 'Nama minimal 3 karakter.';
    }

    // Validasi Email (opsional)
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format e-mail tidak valid.';
    }

    // Validasi Tanggal Lahir
    if ($tanggal !== '' && !validasiTanggal($tanggal)) {
        $errors[] = 'Format tanggal lahir tidak valid (gunakan format YYYY-MM-DD).';
    }

    // Validasi Jenis Kelamin
    if (!empty($jenis_kelamin) && !in_array($jenis_kelamin, ['L', 'P'])) {
        $errors[] = 'Jenis kelamin tidak valid.';
    }

    /*
    kondisi di bawah ini hanya dikerjakan jika ada error, 
    simpan nilai lama dan pesan error, lalu redirect (konsep PRG)
    */
    if (!empty($errors)) {
        $_SESSION['old_edit_biodata'] = [
            'nama' => $nama,
            'tempat' => $tempat,
            'tanggal' => $tanggal,
            'alamat' => $alamat,
            'email' => $email,
            'telepon' => $telepon,
            'jenis_kelamin' => $jenis_kelamin,
            'program_studi' => $program_studi,
            'hobi' => $hobi
        ];
        
        $_SESSION['flash_error'] = implode('<br>', $errors);
        redirect_ke('edit.php?id=' . (int)$id . '&type=mahasiswa');
    }

    /*
    Prepared statement untuk anti SQL injection.
    menyiapkan query UPDATE biodata mahasiswa
    */
    $sql = "UPDATE mahasiswa SET 
            nama = ?, 
            tempat_lahir = ?, 
            tanggal_lahir = ?, 
            alamat = ?, 
            email = ?, 
            telepon = ?, 
            jenis_kelamin = ?, 
            program_studi = ?, 
            hobi = ? 
            WHERE id = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        #jika gagal prepare, kirim pesan error
        $_SESSION['flash_error'] = 'Terjadi kesalahan sistem (prepare gagal).';
        redirect_ke('edit.php?id=' . (int)$id . '&type=mahasiswa');
    }

    #bind parameter dan eksekusi
    $tanggal = $tanggal === '' ? null : $tanggal;
    mysqli_stmt_bind_param($stmt, "sssssssssi", 
        $nama, $tempat, $tanggal, $alamat, $email, $telepon,
        $jenis_kelamin, $program_studi, $hobi, $id
    );

    if (mysqli_stmt_execute($stmt)) { #jika berhasil
        unset($_SESSION['old_edit_biodata']);
        
        /*
        Redirect balik ke read.php dan tampilkan info sukses.
        Sesuai pola PRG (Post-Redirect-Get) - soal nomor 5
        */
        $_SESSION['flash_sukses'] = 'Data biodata mahasiswa berhasil diperbarui.';
        redirect_ke('read.php'); #pola PRG: kembali ke data dan exit()
    } else { #jika gagal
        $_SESSION['old_edit_biodata'] = [
            'nama' => $nama,
            'tempat' => $tempat,
            'tanggal' => $tanggal,
            'alamat' => $alamat,
            'email' => $email,
            'telepon' => $telepon,
            'jenis_kelamin' => $jenis_kelamin,
            'program_studi' => $program_studi,
            'hobi' => $hobi
        ];
        $_SESSION['flash_error'] = 'Data gagal diperbarui. Silakan coba lagi.';
        redirect_ke('edit.php?id=' . (int)$id . '&type=mahasiswa');
    }
    
    #tutup statement
    mysqli_stmt_close($stmt);
}

// Tutup koneksi database
if (isset($conn)) {
    $conn->close();
}
?>