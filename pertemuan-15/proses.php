<?php
session_start();
require __DIR__ . './koneksi.php';
require_once __DIR__ . '/fungsi.php';

#cek method form, hanya izinkan POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  $_SESSION['flash_error'] = 'Akses tidak valid.';
  redirect_ke('index.php#contact');
}

$isBiodataForm = isset($_POST['submit_biodata']);

if (!$isBiodataForm) {
#ambil dan bersihkan nilai dari form
$nama  = bersihkan($_POST['txtNama']  ?? '');
$email = bersihkan($_POST['txtEmail'] ?? '');
$pesan = bersihkan($_POST['txtPesan'] ?? '');
$captcha = bersihkan($_POST['txtCaptcha'] ?? '');
}
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

if ($captcha!=="5") {
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
    'pesan' => $pesan,
    'captcha' => $captcha,
  ];

  $_SESSION['flash_error'] = implode('<br>', $errors);
  redirect_ke('index.php#contact');
}

#menyiapkan query INSERT dengan prepared statement
$sql = "INSERT INTO tbl_tamu (cnama, cemail, cpesan) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
  #jika gagal prepare, kirim pesan error ke pengguna (tanpa detail sensitif)
  $_SESSION['flash_error'] = 'Terjadi kesalahan sistem (prepare gagal).';
  redirect_ke('index.php#contact');
}
#bind parameter dan eksekusi (s = string)
mysqli_stmt_bind_param($stmt, "sss", $nama, $email, $pesan);

if (mysqli_stmt_execute($stmt)) { #jika berhasil, kosongkan old value, beri pesan sukses
  unset($_SESSION['old']);
  $_SESSION['flash_sukses'] = 'Terima kasih, data Anda sudah tersimpan.';
  redirect_ke('index.php#contact'); #pola PRG: kembali ke form / halaman home
} else { #jika gagal, simpan kembali old value dan tampilkan error umum
  $_SESSION['old'] = [
    'nama'  => $nama,
    'email' => $email,
    'pesan' => $pesan,
    'captcha' => $captcha,
  ];
  $_SESSION['flash_error'] = 'Data gagal disimpan. Silakan coba lagi.';
  redirect_ke('index.php#contact');
}
#tutup statement
mysqli_stmt_close($stmt);

$arrBiodata = [
  "nim" => $_POST["txtNim"] ?? "",
  "nama" => $_POST["txtNmLengkap"] ?? "",
  "tempat" => $_POST["txtT4Lhr"] ?? "",
  "tanggal" => $_POST["txtTglLhr"] ?? "",
  "hobi" => $_POST["txtHobi"] ?? "",
  "pasangan" => $_POST["txtPasangan"] ?? "",
  "pekerjaan" => $_POST["txtKerja"] ?? "",
  "ortu" => $_POST["txtNmOrtu"] ?? "",
  "kakak" => $_POST["txtNmKakak"] ?? "",
  "adik" => $_POST["txtNmAdik"] ?? ""
];
$_SESSION["biodata"] = $arrBiodata;


header("location: index.php#about");

} else {

  $dataBiodata = bersihkanDataBiodata($_POST);
  
  $errors = validasiDataBiodata($dataBiodata, $conn);

   if (!empty($errors)) {
    $_SESSION['old_biodata'] = $dataBiodata;
    $_SESSION['flash_error'] = implode('<br>', $errors);
    redirect_ke('index.php#biodata');
  }


  $sql = "INSERT INTO mahasiswa (nim, nama, tempat_lahir, tanggal_lahir, alamat, email, telepon, 
                                jenis_kelamin, program_studi, hobi, pasangan, pekerjaan, 
                                nama_ortu, nama_kakak, nama_adik) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
  $stmt = mysqli_prepare($conn, $sql);

  if (!$stmt) {
    $_SESSION['flash_error'] = 'Terjadi kesalahan sistem (prepare gagal).';
    redirect_ke('index.php#biodata');
  }


  $tanggal = $dataBiodata['tanggal_lahir'] === '' ? null : $dataBiodata['tanggal_lahir'];
  mysqli_stmt_bind_param($stmt, "sssssssssssssss", 
      $dataBiodata['nim'], 
      $dataBiodata['nama'], 
      $dataBiodata['tempat_lahir'], 
      $tanggal, 
      $dataBiodata['alamat'], 
      $dataBiodata['email'], 
      $dataBiodata['telepon'],
      $dataBiodata['jenis_kelamin'], 
      $dataBiodata['program_studi'], 
      $dataBiodata['hobi'], 
      $dataBiodata['pasangan'], 
      $dataBiodata['pekerjaan'],
      $dataBiodata['nama_ortu'], 
      $dataBiodata['nama_kakak'], 
      $dataBiodata['nama_adik']
  );

  if (mysqli_stmt_execute($stmt)) {
    unset($_SESSION['old_biodata']);
    $_SESSION['flash_sukses'] = 'Data biodata mahasiswa berhasil disimpan.';

    $_SESSION["biodata"] = [
      "nim" => $dataBiodata['nim'],
      "nama" => $dataBiodata['nama'],
      "tempat" => $dataBiodata['tempat_lahir'],
      "tanggal" => $dataBiodata['tanggal_lahir'],
      "hobi" => $dataBiodata['hobi'],
      "pasangan" => $dataBiodata['pasangan'],
      "pekerjaan" => $dataBiodata['pekerjaan'],
      "ortu" => $dataBiodata['nama_ortu'],
      "kakak" => $dataBiodata['nama_kakak'],
      "adik" => $dataBiodata['nama_adik']
    ];
    
    redirect_ke('read.php'); #pola PRG: redirect ke halaman baca data (soal nomor 3)
  } else {
    $_SESSION['old_biodata'] = $dataBiodata;
    $_SESSION['flash_error'] = 'Data gagal disimpan. Silakan coba lagi.';
    redirect_ke('index.php#biodata');
  }

  #tutup statement
  mysqli_stmt_close($stmt);
}

// Tutup koneksi database
if (isset($conn)) {
    $conn->close();
}
?>