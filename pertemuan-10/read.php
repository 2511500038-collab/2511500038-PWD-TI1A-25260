<?php
require 'koneksi.php';

$sql = "SELECT * FROM tbl_tamu ORDER BY id DESC";
$q = mysqli_query($conn, $sql);
?>
<table border="1" cellpadding="10" cellspacing="0">
    <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Email</th>
        <th>Pesan</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($q)): ?>
        <tr>
            <td><?= $row['cid']; ?></td>
            <td><?= htmlspesecialchars($row['cnama']); ?></td>
            <td><?= htmlspesecialchars($row['cemail']); ?></td>
            <td><?= nl2br(htmlspesecialchars($row['cpesan'])); ?></td>
        </tr>
    <?php endwhile; ?>
</table>

         