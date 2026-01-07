<?php
function redirect_ke($url)
{
  header("Location: " . $url);
  exit();
}

function bersihkan($str)
{
  return htmlspecialchars(trim($str));
}

function tidakKosong($str)
{
  return strlen(trim($str)) > 0;
}

function formatTanggal($tgl)
{
  return date("d M Y H:i:s", strtotime($tgl));
}

function tampilkanBiodata($conf, $arr)
{
  $html = "";
  foreach ($conf as $k => $v) {
    $label = $v["label"];
    $nilai = bersihkan($arr[$k] ?? '');
    $suffix = $v["suffix"];

    $html .= "<p><strong>{$label}</strong> {$nilai}{$suffix}</p>";
  }
  return $html;
}

// ============================================
// FUNGSI BARU UNTUK CRUD BIODATA MAHASISWA
// ============================================

/**
 * Fungsi untuk validasi NIM (8-15 digit angka)
 */
function validasiNIM($nim) {
    return preg_match('/^[0-9]{8,15}$/', trim($nim));
}

/**
 * Fungsi untuk validasi format tanggal (YYYY-MM-DD)
 */
function validasiTanggal($tanggal) {
    if (empty(trim($tanggal))) {
        return true; // Tanggal kosong diperbolehkan
    }
    
    $pattern = '/^\d{4}-\d{2}-\d{2}$/';
    if (!preg_match($pattern, $tanggal)) {
        return false;
    }
    
    $date = DateTime::createFromFormat('Y-m-d', $tanggal);
    return $date && $date->format('Y-m-d') === $tanggal;
}

/**
 * Fungsi untuk mendapatkan data mahasiswa berdasarkan ID
 */
function getMahasiswaById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM mahasiswa WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
    return $data;
}

/**
 * Fungsi untuk cek apakah NIM sudah ada di database
 */
function isNimExists($conn, $nim, $excludeId = null) {
    $nim = trim($nim);
    
    if ($excludeId) {
        $stmt = $conn->prepare("SELECT id FROM mahasiswa WHERE nim = ? AND id != ?");
        $stmt->bind_param("si", $nim, $excludeId);
    } else {
        $stmt = $conn->prepare("SELECT id FROM mahasiswa WHERE nim = ?");
        $stmt->bind_param("s", $nim);
    }
    
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();
    
    return $exists;
}

/**
 * Fungsi untuk validasi email khusus (boleh kosong)
 */
function validasiEmailMhs($email) {
    $email = trim($email);
    if (empty($email)) {
        return true; // Email boleh kosong
    }
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Fungsi untuk mendapatkan semua data mahasiswa
 */
function getAllMahasiswa($conn, $orderBy = "created_at DESC") {
    $query = "SELECT id, nim, nama, email, telepon, program_studi, created_at 
              FROM mahasiswa 
              ORDER BY {$orderBy}";
    $result = $conn->query($query);
    
    $data = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    return $data;
}

/**
 * Fungsi untuk format tanggal lahir (dari YYYY-MM-DD ke format Indonesia)
 */
function formatTanggalLahir($tanggal) {
    if (empty($tanggal)) {
        return '-';
    }
    
    $bulanIndo = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
        '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];
    
    $tgl = explode('-', $tanggal);
    if (count($tgl) === 3) {
        return $tgl[2] . ' ' . $bulanIndo[$tgl[1]] . ' ' . $tgl[0];
    }
    return $tanggal;
}

/**
 * Fungsi untuk membersihkan dan validasi data form biodata mahasiswa
 */
function bersihkanDataBiodata($postData) {
    return [
        'nim' => bersihkan($postData['txtNim'] ?? ''),
        'nama' => bersihkan($postData['txtNmLengkap'] ?? ''),
        'tempat_lahir' => bersihkan($postData['txtT4Lhr'] ?? ''),
        'tanggal_lahir' => bersihkan($postData['txtTglLhr'] ?? ''),
        'alamat' => bersihkan($postData['txtAlamat'] ?? ''),
        'email' => bersihkan($postData['txtEmailMhs'] ?? ''),
        'telepon' => bersihkan($postData['txtTelepon'] ?? ''),
        'jenis_kelamin' => bersihkan($postData['txtJenisKelamin'] ?? ''),
        'program_studi' => bersihkan($postData['txtProdi'] ?? ''),
        'hobi' => bersihkan($postData['txtHobi'] ?? ''),
        'pasangan' => bersihkan($postData['txtPasangan'] ?? ''),
        'pekerjaan' => bersihkan($postData['txtKerja'] ?? ''),
        'nama_ortu' => bersihkan($postData['txtNmOrtu'] ?? ''),
        'nama_kakak' => bersihkan($postData['txtNmKakak'] ?? ''),
        'nama_adik' => bersihkan($postData['txtNmAdik'] ?? '')
    ];
}

/**
 * Fungsi untuk validasi data biodata mahasiswa
 */
function validasiDataBiodata($data, $conn, $excludeId = null) {
    $errors = [];
    
    // Validasi NIM
    if (!tidakKosong($data['nim'])) {
        $errors[] = 'NIM wajib diisi.';
    } elseif (!validasiNIM($data['nim'])) {
        $errors[] = 'Format NIM tidak valid (8-15 digit angka).';
    } elseif (isNimExists($conn, $data['nim'], $excludeId)) {
        $errors[] = 'NIM sudah terdaftar.';
    }
    
    // Validasi Nama
    if (!tidakKosong($data['nama'])) {
        $errors[] = 'Nama lengkap wajib diisi.';
    } elseif (mb_strlen($data['nama']) < 3) {
        $errors[] = 'Nama minimal 3 karakter.';
    }
    
    // Validasi Email (opsional)
    if (!empty($data['email']) && !validasiEmailMhs($data['email'])) {
        $errors[] = 'Format email tidak valid.';
    }
    
    // Validasi Tanggal Lahir
    if (!empty($data['tanggal_lahir']) && !validasiTanggal($data['tanggal_lahir'])) {
        $errors[] = 'Format tanggal lahir tidak valid (gunakan format YYYY-MM-DD).';
    }
    
    // Validasi Jenis Kelamin
    if (!empty($data['jenis_kelamin']) && !in_array($data['jenis_kelamin'], ['L', 'P'])) {
        $errors[] = 'Jenis kelamin tidak valid.';
    }
    
    return $errors;
}

/**
 * Fungsi untuk konfigurasi tampilan biodata di section#about
 */
function konfigurasiBiodata() {
    return [
        "nim" => ["label" => "NIM", "suffix" => ""],
        "nama" => ["label" => "Nama", "suffix" => ""],
        "tempat" => ["label" => "Tempat Lahir", "suffix" => ""],
        "tanggal" => ["label" => "Tanggal Lahir", "suffix" => ""],
        "hobi" => ["label" => "Hobi", "suffix" => ""],
        "pasangan" => ["label" => "Nama Pasangan", "suffix" => ""],
        "pekerjaan" => ["label" => "Pekerjaan", "suffix" => ""],
        "ortu" => ["label" => "Nama Orang Tua", "suffix" => ""],
        "kakak" => ["label" => "Nama Kakak", "suffix" => ""],
        "adik" => ["label" => "Nama Adik", "suffix" => ""]
    ];
}
?>