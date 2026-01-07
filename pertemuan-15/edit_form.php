<?php
require_once 'koneksi.php';

$id = $_GET['id'] ?? 0;
$data = null;

// Ambil data berdasarkan ID
if ($id) {
    $stmt = $conn->prepare("SELECT * FROM mahasiswa WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
    
    if (!$data) {
        header('Location: tampil_data.php');
        exit;
    }
} else {
    header('Location: tampil_data.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Biodata Mahasiswa</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, select, textarea { width: 100%; padding: 8px; }
        button { padding: 10px 20px; margin-right: 10px; cursor: pointer; }
        .btn-kirim { background: #007bff; color: white; border: none; }
        .btn-batal { background: #6c757d; color: white; border: none; }
        .message { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Biodata Mahasiswa</h1>
        
        <form action="proses_update.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $data['id']; ?>">
            
            <div class="form-group">
                <label for="nim">NIM *</label>
                <input type="text" id="nim" name="nim" value="<?php echo htmlspecialchars($data['nim']); ?>" 
                       readonly style="background-color: #e9ecef;">
            </div>
            
            <div class="form-group">
                <label for="nama">Nama Lengkap *</label>
                <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($data['nama']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea id="alamat" name="alamat" rows="3"><?php echo htmlspecialchars($data['alamat']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($data['email']); ?>">
            </div>
            
            <div class="form-group">
                <label for="telepon">Telepon</label>
                <input type="text" id="telepon" name="telepon" value="<?php echo htmlspecialchars($data['telepon']); ?>">
            </div>
            
            <div class="form-group">
                <label for="jenis_kelamin">Jenis Kelamin</label>
                <select id="jenis_kelamin" name="jenis_kelamin">
                    <option value="">- Pilih -</option>
                    <option value="L" <?php echo $data['jenis_kelamin'] == 'L' ? 'selected' : ''; ?>>Laki-laki</option>
                    <option value="P" <?php echo $data['jenis_kelamin'] == 'P' ? 'selected' : ''; ?>>Perempuan</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="program_studi">Program Studi</label>
                <input type="text" id="program_studi" name="program_studi" 
                       value="<?php echo htmlspecialchars($data['program_studi']); ?>">
            </div>
            
            <button type="submit" name="submit" class="btn-kirim">Kirim</button>
            <button type="button" class="btn-batal" onclick="window.location.href='tampil_data.php'">Batal</button>
        </form>
    </div>
</body>
</html>
<?php $conn->close(); ?>