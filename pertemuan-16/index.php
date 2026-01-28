 <?php
session_start();
require_once __DIR__ . '/fungsi.php';
require_once __DIR__ . '/koneksi.php'; // Tambahkan koneksi database

// Untuk pesan status CRUD
$status_crud = $_SESSION['status_crud'] ?? '';
$message_crud = $_SESSION['message_crud'] ?? '';
$old_input = $_SESSION['old_input'] ?? [];

// Hapus session setelah ditampilkan
unset($_SESSION['status_crud'], $_SESSION['message_crud'], $_SESSION['old_input']);

// Untuk pesan kontak (dari form kontak)
$flash_sukses = $_SESSION['flash_sukses'] ?? '';
$flash_error = $_SESSION['flash_error'] ?? '';
$old_kontak = $_SESSION['old'] ?? [];

unset($_SESSION['flash_sukses'], $_SESSION['flash_error'], $_SESSION['old']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CRUD Biodata Dosen</title>
  <link rel="stylesheet" href="style.css">
  <style>
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
    .navigation {
      text-align: center;
      margin: 20px 0;
    }
    .btn {
      display: inline-block;
      padding: 10px 20px;
      background-color: #4CAF50;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      margin: 5px;
    }
    .btn-view {
      background-color: #2196F3;
    }
    .form-group {
      margin-bottom: 15px;
    }
    .readonly-input {
      background-color: #f5f5f5;
      cursor: not-allowed;
    }
  </style>
</head>

<body>
  <header>
    <h1>CRUD Biodata Dosen</h1>
    <button class="menu-toggle" id="menuToggle" aria-label="Toggle Navigation">
      &#9776;
    </button>
    <nav>
      <ul>
        <li><a href="#home">Beranda</a></li>
        <li><a href="#biodata">Input Biodata</a></li>
        <li><a href="read.php">Data Dosen</a></li> <!-- LINK KE FILE PEMBACA -->
        <li><a href="#contact">Kontak</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <section id="home">
      <h2>Selamat Datang di Aplikasi CRUD Biodata Dosen</h2>
      <p>Aplikasi ini digunakan untuk mengelola data dosen (Create, Read, Update, Delete).</p>
      
      <?php if ($status_crud && $message_crud): ?>
        <div class="status-message <?php echo $status_crud; ?>">
          <?php echo htmlspecialchars($message_crud); ?>
        </div>
      <?php endif; ?>
      
      <div class="navigation">
        <a href="#biodata" class="btn">Tambah Data Dosen</a>
        <a href="read.php" class="btn btn-view">Lihat Data Dosen</a>
      </div>
    </section>

    <!-- SECTION BIODATA DOSEN (Nomor 1 & 2) -->
    <section id="biodata">
      <h2>Form Biodata Dosen</h2>
      
      <form action="proses_bio.php" method="POST">
        <div class="form-group">
          <label for="txtNidn"><span>NIDN:</span></label>
          <input type="text" id="txtNidn" name="txtNidn" 
                 placeholder="Masukkan NIDN (10-20 angka)" 
                 pattern="[0-9]{10,20}"
                 title="NIDN harus 10-20 digit angka"
                 value="<?php echo isset($old_input['nidn']) ? htmlspecialchars($old_input['nidn']) : ''; ?>"
                 required>
        </div>

        <div class="form-group">
          <label for="txtNamaDosen"><span>Nama Dosen:</span></label>
          <input type="text" id="txtNamaDosen" name="txtNamaDosen" 
                 placeholder="Masukkan Nama Lengkap"
                 value="<?php echo isset($old_input['nama_dosen']) ? htmlspecialchars($old_input['nama_dosen']) : ''; ?>"
                 required>
        </div>

        <div class="form-group">
          <label><span>Jenis Kelamin:</span></label>
          <div class="radio-group">
            <label>
              <input type="radio" name="txtJenisKelamin" value="L" 
                     <?php echo (isset($old_input['jenis_kelamin']) && $old_input['jenis_kelamin'] == 'L') ? 'checked' : ''; ?>
                     required> Laki-laki
            </label>
            <label>
              <input type="radio" name="txtJenisKelamin" value="P"
                     <?php echo (isset($old_input['jenis_kelamin']) && $old_input['jenis_kelamin'] == 'P') ? 'checked' : ''; ?>> Perempuan
            </label>
          </div>
        </div>

        <div class="form-group">
          <label for="txtTanggalLahir"><span>Tanggal Lahir:</span></label>
          <input type="date" id="txtTanggalLahir" name="txtTanggalLahir"
                 value="<?php echo isset($old_input['tanggal_lahir']) ? htmlspecialchars($old_input['tanggal_lahir']) : ''; ?>"
                 required>
        </div>

        <div class="form-group">
          <label for="txtEmail"><span>Email:</span></label>
          <input type="email" id="txtEmail" name="txtEmail" 
                 placeholder="Masukkan Email"
                 value="<?php echo isset($old_input['email']) ? htmlspecialchars($old_input['email']) : ''; ?>"
                 required>
        </div>

        <div class="form-group">
          <label for="txtNoTelepon"><span>No Telepon:</span></label>
          <input type="tel" id="txtNoTelepon" name="txtNoTelepon" 
                 placeholder="Masukkan No Telepon"
                 pattern="[0-9]{10,15}"
                 title="No telepon 10-15 digit angka"
                 value="<?php echo isset($old_input['no_telepon']) ? htmlspecialchars($old_input['no_telepon']) : ''; ?>"
                 required>
        </div>

        <div class="form-group">
          <label for="txtAlamat"><span>Alamat:</span></label>
          <textarea id="txtAlamat" name="txtAlamat" rows="3" 
                    placeholder="Masukkan Alamat"
                    required><?php echo isset($old_input['alamat']) ? htmlspecialchars($old_input['alamat']) : ''; ?></textarea>
        </div>

        <div class="form-group">
          <label for="selJabatan"><span>Jabatan:</span></label>
          <select id="selJabatan" name="selJabatan" required>
            <option value="">Pilih Jabatan</option>
            <option value="Asisten Ahli" <?php echo (isset($old_input['jabatan']) && $old_input['jabatan'] == 'Asisten Ahli') ? 'selected' : ''; ?>>Asisten Ahli</option>
            <option value="Lektor" <?php echo (isset($old_input['jabatan']) && $old_input['jabatan'] == 'Lektor') ? 'selected' : ''; ?>>Lektor</option>
            <option value="Lektor Kepala" <?php echo (isset($old_input['jabatan']) && $old_input['jabatan'] == 'Lektor Kepala') ? 'selected' : ''; ?>>Lektor Kepala</option>
            <option value="Guru Besar" <?php echo (isset($old_input['jabatan']) && $old_input['jabatan'] == 'Guru Besar') ? 'selected' : ''; ?>>Guru Besar</option>
          </select>
        </div>

        <div class="form-group">
          <label for="selPendidikan"><span>Pendidikan Terakhir:</span></label>
          <select id="selPendidikan" name="selPendidikan" required>
            <option value="">Pilih Pendidikan</option>
            <option value="S1" <?php echo (isset($old_input['pendidikan_terakhir']) && $old_input['pendidikan_terakhir'] == 'S1') ? 'selected' : ''; ?>>S1</option>
            <option value="S2" <?php echo (isset($old_input['pendidikan_terakhir']) && $old_input['pendidikan_terakhir'] == 'S2') ? 'selected' : ''; ?>>S2</option>
            <option value="S3" <?php echo (isset($old_input['pendidikan_terakhir']) && $old_input['pendidikan_terakhir'] == 'S3') ? 'selected' : ''; ?>>S3</option>
          </select>
        </div>

        <button type="submit">Kirim</button>
        <button type="reset">Batal</button>
      </form>
      
      <div class="navigation">
        <p>Untuk melihat data yang sudah disimpan:</p>
        <a href="read.php" class="btn btn-view">Lihat Data Dosen</a>
      </div>
    </section>

    <!-- HAPUS SECTION ABOUT (tidak sesuai dengan nomor 3) -->
    <!-- Data dosen akan ditampilkan di file terpisah read.php -->

    <!-- SECTION KONTAK (tetap dipertahankan) -->
    <section id="contact">
      <h2>Kontak Kami</h2>

      <?php if (!empty($flash_sukses)): ?>
        <div style="padding:10px; margin-bottom:10px; background:#d4edda; color:#155724; border-radius:6px;">
          <?= $flash_sukses; ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($flash_error)): ?>
        <div style="padding:10px; margin-bottom:10px; background:#f8d7da; color:#721c24; border-radius:6px;">
          <?= $flash_error; ?>
        </div>
      <?php endif; ?>

      <form action="proses.php" method="POST">
        <div class="form-group">
          <label for="txtNama"><span>Nama:</span></label>
          <input type="text" id="txtNama" name="txtNama" placeholder="Masukkan nama"
            required autocomplete="name"
            value="<?= isset($old_kontak['nama']) ? htmlspecialchars($old_kontak['nama']) : '' ?>">
        </div>

        <div class="form-group">
          <label for="txtEmailKontak"><span>Email:</span></label>
          <input type="email" id="txtEmailKontak" name="txtEmail" placeholder="Masukkan email"
            required autocomplete="email"
            value="<?= isset($old_kontak['email']) ? htmlspecialchars($old_kontak['email']) : '' ?>">
        </div>

        <div class="form-group">
          <label for="txtPesan"><span>Pesan Anda:</span></label>
          <textarea id="txtPesan" name="txtPesan" rows="4" placeholder="Tulis pesan anda..."
            required><?= isset($old_kontak['pesan']) ? htmlspecialchars($old_kontak['pesan']) : '' ?></textarea>
          <small id="charCount">0/200 karakter</small>
        </div>

        <div class="form-group">
          <label for="txtCaptcha"><span>Captcha 2 + 3 = ?</span></label>
          <input type="number" id="txtCaptcha" name="txtCaptcha" placeholder="Jawab Pertanyaan..."
            required
            value="<?= isset($old_kontak['captcha']) ? htmlspecialchars($old_kontak['captcha']) : '' ?>">
        </div>

        <button type="submit">Kirim</button>
        <button type="reset">Batal</button>
      </form>

      <br>
      <hr>
      <h2>Yang menghubungi kami</h2>
      <?php include 'read_inc.php'; ?>
    </section>
  </main>

  <footer>
    <p>&copy; 2025 CRUD Biodata Dosen - Aplikasi UAS PWD</p>
  </footer>

  <script src="script.js"></script>
  <script>
    // Validasi form biodata dosen
    document.addEventListener('DOMContentLoaded', function() {
      const formBiodata = document.querySelector('form[action="proses_bio.php"]');
      if (formBiodata) {
        formBiodata.addEventListener('submit', function(e) {
          const nidn = document.getElementById('txtNidn').value;
          const telepon = document.getElementById('txtNoTelepon').value;
          
          // Validasi NIDN
          if (!/^[0-9]{10,20}$/.test(nidn)) {
            alert('NIDN harus terdiri dari 10-20 angka');
            e.preventDefault();
            return false;
          }
          
          // Validasi No Telepon
          if (!/^[0-9]{10,15}$/.test(telepon)) {
            alert('No telepon harus 10-15 digit angka');
            e.preventDefault();
            return false;
          }
          
          return true;
        });
      }
    });
  </script>
</body>
</html>