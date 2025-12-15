<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] != "POST") {
  $_SESSION["error"] = "Akses tidak valid.";
  redirect_ke('index.php#contact');
}

$arrContact = [
  $nama = bersihkan($_POST["txtNama"] ?? ""),
  $email = bersihkan($_POST["txtEmail"] ?? ""),
  $pesan = bersihkan($_POST["txtPesan"] ?? "")


  $errors = [];

  if ($nama === "") {
    $errors[] = 'Nama wajib diisi.';
  }
  
  if ($email === "") {
    $errors[] = 'Email wajib diisi.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Format email tidak valid.';
  }

  if ($pesan === "") {
    $errors[] = 'Pesan wajib diisi.';
  }





  if (!empty($errors)) {
    $_SESSION["old"] = [
      "nama" => $nama,
      "email" => $email,
      "pesan" => $pesan,
    ];

    $_SESSION["flash_error"] = implode("<br>", $errors);
    redirect_ke('index.php#contact');
  }
  
  $sql = "INSERT INTO tbl_tamu (nama, email, pesan) VALUES (?, ?, ?)";
  $stmt = mysqli_prepare($conn, $sql);

  if (!$stmt) {
    $_SESSION["flash_error"] = "Gagal menyiapkan pernyataan: " . mysqli_error($conn);
    redirect_ke('index.php#contact');
  }

  mysqli_stmt_bind_param($stmt, "sss", $nama, $email, $pesan);

  if (mysqli_stmt_execute($stmt)) {
    unset($_SESSION['old']);
    $_SESSION['flash_sukses'] = "Pesan Anda telah terkirim. Terima kasih!";
    redirect_ke('index.php#contact');
  } else {

    $_SESSION["old"] = [
      "nama" => $nama,
      "email" => $email,
      "pesan" => $pesan,
    ];
    $_SESSION["flash_error"] = 'Data gagal disimpan. Silakan coba lagi.';
    redirect_ke('index.php#contact');
  }

  myqlyli_stmt_close($stmt);

$arrContact;

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
