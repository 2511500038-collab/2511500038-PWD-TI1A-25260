 <?php
session_start();
require __DIR__ . '/koneksi.php';
require_once __DIR__ . '/fungsi.php';

# Deteksi tipe data yang akan dihapus
$type = $_GET['type'] ?? 'tamu'; // Default: tamu, bisa: mahasiswa

if ($type === 'tamu') {
    // ============================================
    // ============================================
    
    #validasi cid wajib angka dan > 0
    $cid = filter_input(INPUT_GET, 'cid', FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1]
    ]);

    if (!$cid) {
        $_SESSION['flash_error'] = 'CID Tidak Valid.';
        redirect_ke('read.php');
    }

    /*
        Prepared statement untuk anti SQL injection.
        menyiapkan query DELETE dengan prepared statement 
        (WAJIB WHERE cid = ?)
    */
    $stmt = mysqli_prepare($conn, "DELETE FROM tbl_tamu WHERE cid = ?");
    if (!$stmt) {
        #jika gagal prepare, kirim pesan error (tanpa detail sensitif)
        $_SESSION['flash_error'] = 'Terjadi kesalahan sistem (prepare gagal).';
        redirect_ke('read.php');
    }

    #bind parameter dan eksekusi (i = integer)
    mysqli_stmt_bind_param($stmt, "i", $cid);

    if (mysqli_stmt_execute($stmt)) { #jika berhasil
        /*
            Redirect balik ke read.php dan tampilkan info sukses.
        */
        $_SESSION['flash_sukses'] = 'Data tamu berhasil dihapus.';
    } else { #jika gagal
        $_SESSION['flash_error'] = 'Data gagal dihapus. Silakan coba lagi.';
    }
    #tutup statement
    mysqli_stmt_close($stmt);

} else if ($type === 'mahasiswa') {
    // ============================================
    // BAGIAN 2: PROSES DELETE BIODATA MAHASISWA (SOAL NOMOR 7)
    // ============================================
    
    #validasi id wajib angka dan > 0
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1]
    ]);

    if (!$id) {
        $_SESSION['flash_error'] = 'ID Mahasiswa Tidak Valid.';
        redirect_ke('read.php');
    }

    /*
        Prepared statement untuk anti SQL injection.
        menyiapkan query DELETE biodata mahasiswa
        (WAJIB WHERE id = ?)
    */
    $stmt = mysqli_prepare($conn, "DELETE FROM mahasiswa WHERE id = ?");
    if (!$stmt) {
        #jika gagal prepare, kirim pesan error
        $_SESSION['flash_error'] = 'Terjadi kesalahan sistem (prepare gagal).';
        redirect_ke('read.php');
    }

    #bind parameter dan eksekusi (i = integer)
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) { #jika berhasil
        /*
            Redirect balik ke read.php dan tampilkan info sukses.
            Sesuai soal nomor 7 & 8
        */
        $_SESSION['flash_sukses'] = 'Data mahasiswa berhasil dihapus.';
    } else { #jika gagal
        $_SESSION['flash_error'] = 'Data gagal dihapus. Silakan coba lagi.';
    }
    #tutup statement
    mysqli_stmt_close($stmt);
} else {
    $_SESSION['flash_error'] = 'Tipe data tidak valid.';
}

# Konsep PRG: selalu redirect setelah POST/PUT/DELETE
redirect_ke('read.php');

// Tutup koneksi database
if (isset($conn)) {
    $conn->close();
}
?>