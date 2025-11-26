<?php
session_start();

 $sesnim = "";
if (isset($_SESSION["sesnim"])): $sesnim = $_SESSION["sesnim"]; endif;

$sesnama = "";
if (isset($_SESSION["sesnama"])): $sesnama = $_SESSION["sesnama"]; endif;

$sestempat = "";
if (isset($_SESSION["sestempat"])): $sestempat = $_SESSION["sestempat"]; endif;

$sestgllahir = "";
if (isset($_SESSION["sestgllahir"])): $sestgllahir = $_SESSION["sestgllahir"]; endif;

$seshobi = "";
if (isset($_SESSION["seshobi"])): $seshobi = $_SESSION["seshobi"]; endif;

$sespasangan = "";
if (isset($_SESSION["sespasangan"])): $sespasangan = $_SESSION["sespasangan"]; endif;

$sespekerjaan = "";
if (isset($_SESSION["sespekerjaan"])): $sespekerjaan = $_SESSION["sespekerjaan"]; endif;

$sesortu = "";
if (isset($_SESSION["sesortu"])): $sesortu = $_SESSION["sesortu"]; endif;

$seskakak = "";
if (isset($_SESSION["seskakak"])): $seskakak = $_SESSION["seskakak"]; endif;

$sesadik = "";
if (isset($_SESSION["sesadik"])): $sesadik = $_SESSION["sesadik"]; endif;
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Judul Halaman</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header>
    <h1>Pendaftaran profil Pengunjung</h1>
    <button class="menu-toggle" id="menuToggle" aria-label="Toggle Navigation">
      &#9776;
    </button>
    <nav>
      <ul>
        <li><a href="#home">Beranda</a></li>
        <li><a href="#about">Tentang</a></li>
        <li><a href="#contact">Pendaftaran Profil Pengunjung</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <section id="home">
      <h2>Selamat Datang</h2>
      <?php
      echo "halo dunia!<br>";
      echo "nama saya randy";
      ?>
      <p>Ini contoh paragraf HTML.</p>
    </section>

    <section id="about">
      <?php
        $nim = 2511500038;
        $nama = "Randy orlando";
        $tempatLahir = "Pangkalpinang";
        $tanggalLahir = "14 agustus 2007";
        $hobi = "digital painting";
        $pasangan = "belum ada";
        $pekerjaan = "siswa di ISB Atma Luhur";
        $ortu = "Bapak Mulyadi dan Ibu Fong Siauw Yin";
        $namaKakak = "-";
        $namaAdik = "Nelsia Fadia Mulyaputeri";
      ?>
      <h2>Tentang Saya</h2>
      <p><strong>NIM:</strong>
        <?php
        echo $NIM;
        ?>
      </p>
      <p><strong>Nama Lengkap:</strong>
        <?php
        echo $Nama;
        ?> &#128526;
      </p>
      <p><strong>Tempat Lahir:</strong> <?php echo $tempat; ?></p>
      <p><strong>Tanggal Lahir:</strong> 14 agustus 2007</p>
      <p><strong>Hobi:</strong> digital painting &#11088;</p>
      <p><strong>Pasangan:</strong> Belum ada &#128578;</p>
      <p><strong>Pekerjaan:</strong> siswa di ISB Atma Luhur &copy; 2025</p>
      <p><strong>Nama Orang Tua:</strong> Bapak hendry dan Ibu erly</p>
      <p><strong>Nama Kakak:</strong> - </p>
      <p><strong>Nama Adik:</strong> joselyn </p>
    </section>

    <section id="contact">
      <h2>Pendaftaran Profil Pengunjung</h2>
      <form action="proses.php" method="POST">

       <label for="nim">NIM:</label>
        <input type="text" id="nim" name="nim" required>
        <label for="nama">Nama Lengkap:</label>
        <input type="text" id="nama" name="nama" required>
        <label for="tempat">Tempat Lahir:</label>
        <input type="text" id="tempat" name="tempat" required>
        <label for="tanggallahir">Tanggal Lahir:</label>
        <input type="date" id="tanggallahir" name="tanggallahir" required>
        <label for="hobi">Hobi:</label>
        <input type="text" id="hobi" name="hobi" required>
        <label for="pasangan">Pasangan:</label>
        <input type="text" id="pasangan" name="pasangan">
        <label for="pekerjaan">Pekerjaan:</label>
        <input type="text" id="pekerjaan" name="pekerjaan">
        <label for="ortu">Nama Orang Tua:</label>
        <input type="text" id="ortu" name="ortu">
        <label for="kakak">Nama Kakak:</label>
        <input type="text" id="kakak" name="kakak">
        <label for="adik">Nama Adik:</label>
        <input type="text" id="adik" name="adik">
        <br>
        <button type="submit">Kirim</button>
        <button type="reset">Batal</button>
      </form>

        <section id="contact">
        <h2>Kontak kami</h2>
        <form action="proses.php" method="POST">

        <label for="txtNama"><span>Nama:</span>
          <input type="text" id="txtNama" name="txtNama" placeholder="Masukkan nama" required autocomplete="name">
        </label>

        <label for="txtEmail"><span>Email:</span>
          <input type="email" id="txtEmail" name="txtEmail" placeholder="Masukkan email" required autocomplete="email">
        </label>

        <label for="txtPesan"><span>Pesan Anda:</span>
          <textarea id="txtPesan" name="txtPesan" rows="4" placeholder="Tulis pesan anda..." required></textarea>
          <small id="charCount">0/200 karakter</small>
        </label>


        <button type="submit">Kirim</button>
        <button type="reset">Batal</button>
      </form>

      <?php if (!empty($sesnama)): ?>
        <br><hr>
        <h2>Yang menghubungi kami</h2>
        <p><strong>Nama :</strong> <?php echo $sesnama ?></p>
        <p><strong>Email :</strong> <?php echo $sesemail ?></p>
        <p><strong>Pesan :</strong> <?php echo $sespesan ?></p>
      <?php endif; ?>



    </section>
  </main>

  <footer>
    <p>&copy; 2025 Randy orlando [2511500038]</p>
  </footer>

  <script src="script.js"></script>
</body>

</html>